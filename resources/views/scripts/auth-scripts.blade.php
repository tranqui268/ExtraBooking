<script>

    let userRole = null;
    let userProfile = null;
    let currentPhone = '';
    let countdownTimer = null;

    function getCsrfTokenAsync() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: 'http://localhost:8000/sanctum/csrf-cookie',
                type: 'GET',
                xhrFields: { withCredentials: true },
                success: function () {
                    console.log('CSRF token fetched');
                    resolve();
                },
                error: function (xhr) {
                    $('#error').text('Error fetching CSRF token: ' + xhr.responseText);
                    reject(xhr);
                }
            });
        });
    }

    function performLogin() {
        return new Promise((resolve, reject) => {
            $.ajax({
                url: '{{ url("/api/login") }}',
                method: 'POST',
                data: {
                    email: $('#email').val(),
                    password: $('#password').val(),
                    remember: $('#remember').is(':checked')
                },
                xhrFields: { withCredentials: true },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function (response) {
                    resolve(response);
                },
                error: function (xhr) {
                    reject(xhr);
                }
            });
        });
    }

    async function loginAsync() {
        try {
            await getCsrfTokenAsync();
            console.log('CSRF token ready');

            const response = await performLogin();
            const isRemember = $('#remember').is(':checked');

            userRole = response.user.role;
            $('#message').text(response.message);
            window.location.href = getDashboardUrl(userRole);
        } catch (xhr) {
            if (xhr.responseJSON) {
                $('#error').text('Login failed: ' + xhr.responseJSON.message);
            } else {
                $('#error').text('Error fetching CSRF token: ' + xhr.responseText);
            }
        }
    }

    function performRegister() {
        return new Promise((resolve, reject) => {
            let email = $('#email').val();
            let name = $('#name').val();
            let phone = $('#phone').val();
            let password = $('#password').val();
            let confirmPassword = $('#confirmPassword').val();

            const errorIds = ['#nameError', '#emailError', '#passwordError', '#passwordConfirmError', '#phoneError'];
            errorIds.forEach(id => $(id).text(''));

            if (validateInput(email, name, phone, password, confirmPassword)) {
                return;
            }

            $.ajax({
                url: '{{ url("/api/register") }}',
                type: 'POST',
                data: {
                    email: email,
                    name: name,
                    phone: phone,
                    password: password,
                    password_confirmation: confirmPassword,
                },
                xhrFields: { withCredentials: true },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                success: function (response) {
                    resolve(response);
                },
                error: function (xhr) {
                    reject(xhr);
                }
            });
        });
    }

    async function registerAsync() {
        try {
            await getCsrfTokenAsync();

            const response = await performRegister();

            userRole = response.user.role;

            Swal.fire({
                icon: 'success',
                title: 'Đăng ký thành công',
                text: response.message,
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                window.location.href = getDashboardUrl(userRole);
            });
        } catch (error) {
            const errorMessage = error.responseJSON?.errors
                ? Object.values(error.responseJSON.errors).flat().join(', ')
                : error.responseJSON?.message || 'Lỗi đăng ký';
            Swal.fire({
                icon: 'error',
                title: 'Đăng ký thất bại',
                text: errorMessage
            });
            console.error('Lỗi đăng ký:', error);
        }
    }

    async function logout() {
        try {
            await getCsrfTokenAsync();

            await $.ajax({
                url: '{{ url("/api/logout") }}',
                type: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                xhrFields: { withCredentials: true }
            });


            Swal.fire({
                icon: 'success',
                title: 'Đăng xuất thành công',
                timer: 1500,
                showConfirmButton: false
            }).then(() => {
                $('#user-name').text('Chào user');
                window.location.href = '/login';
            });

        } catch (error) {
            Swal.fire({
                icon: 'error',
                title: 'Đăng xuất thất bại',
                text: error.responseJSON?.message || error
            });
            console.error('Lỗi đăng xuất:', error);
        }
    }

    async function fetchUser() {
        try {
            const response = await $.ajax({
                url: '{{ url("/api/user") }}',
                type: 'GET',
                xhrFields: { withCredentials: true },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            userRole = response.user.role;
            userProfile = response.profile;
            if (userRole === 'user') {
                $('#user-name').text(`Chào ${userProfile.name}`).attr('data-id', userProfile.id);
            } else {
                updateMenuByRole(userRole);
            }
        } catch (error) {
            if (error.status === 401 || error.status === 403) {
                userRole = null;
                userProfile = null;
                Swal.fire({
                    icon: 'warning',
                    title: 'Phiên hết hạn hoặc không có quyền',
                    text: 'Vui lòng đăng nhập lại',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '/login';
                });
            }

        }
    }

    function sendOtp() {
        const phone = document.getElementById('phone').value.trim();
        const phoneError = document.getElementById('phoneError');
        phoneError.textContent = '';

        if (!phone) {
            phoneError.textContent = 'Số điện thoại không được để trống';
            return;
        }

        const sendBtn = event.target;
        sendBtn.disabled = true;
        sendBtn.textContent = 'Đang gửi...';

        $.ajax({
            url: "{{ url('api/auth/otp/send') }}",
            method: 'POST',
            data: {
                phone: phone
            },
            success: function (response) {
                currentPhone = phone;
                showOtpStep();
                Swal.fire({
                    icon: 'success',
                    title: 'Thành công',
                    text: 'Đã gửi OTP vui lòng kiểm tra',
                });
                startCountdown();
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không thể gửi OTP. Vui lòng thử lại',
                });
            },
            complete: function () {
                sendBtn.disabled = false;
                sendBtn.textContent = 'Gửi mã OTP';
            }
        });
    }

    function showOtpStep() {
        document.getElementById('phone-step').classList.add('hidden');
        document.getElementById('otp-step').classList.remove('hidden');
        document.querySelector('.otp-input').focus();
    }

    function startCountdown() {
        let timeLeft = 60;
        const timerText = document.getElementById('timer-text');
        const countdownSpan = document.getElementById('countdown');
        const resendBtn = document.getElementById('resend-btn');

        timerText.classList.remove('hidden');
        resendBtn.classList.add('hidden');

        if (countdownTimer !== null) {
            clearInterval(countdownTimer);
        }

        countdownTimer = setInterval(function () {
            timeLeft--;
            countdownSpan.textContent = timeLeft;

            if (timeLeft <= 0) {
                clearInterval(countdownTimer);
                countdownTimer = null;
                timerText.classList.add('hidden');
                resendBtn.classList.remove('hidden');
            }
        }, 1000);
    }

    function updateMenuByRole(role) {
        $('.nav-link').hide();
        if (role === 'admin') {
            $('#admin-dashboard, #user-menu, #service-menu, #customer-menu, #booking-menu, #employee-menu').show();
        } else if (role === 'employee') {
            $('#schedule-menu').show();
        }
    }

    function getDashboardUrl(role) {
        if (role === 'admin') {
            return '/admin';
        }

        if (role === 'employee') {
            return '/employee';
        }

        return '/bookings';
    }

    function validateInput(email, name, phone, password, confirmPassword) {
        let specialCharRegex = /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]+/;
        let emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
        let phoneRegex = /^0\d{9}$/;
        let hasError = false;

        if (!email) {
            $('#emailError').text('Email không được để trống.');
            hasError = true;
        } else if (!emailRegex.test(email)) {
            $('#emailError').text('Email không đúng định dạng.');
            hasError = true;
        } else if (specialCharRegex.test(email.split('@')[0])) {
            $('#emailError').text('Phần trước @ của email không được chứa ký tự đặc biệt.');
            hasError = true;
        }

        if (!name) {
            $('#nameError').text('Tên không được để trống');
            hasError = true;
        } else if (specialCharRegex.test(name)) {
            $('#nameError').text('Tên không được chứa ký tự đặc biệt');
            hasError = true;
        }

        if (!phone) {
            $('#phoneError').text('Số điện thoại không được để trống');
            hasError = true;
        } else if (!phoneRegex.test(phone)) {
            $('#phoneError').text('Số điện thoại không hợp lệ');
            hasError = true;
        }

        if (!password) {
            $('#passwordError').text('Mật khẩu không được để trống.');
            hasError = true;
        } else if (password.length < 8) {
            $('#passwordError').text('Mật khẩu ít nhất 8 ký tự.');
            hasError = true;
        }

        if (!confirmPassword) {
            $('#passwordConfirmError').text('Xác nhận mật khẩu không được để trống.');
            hasError = true;
        } else if (password !== confirmPassword) {
            $('#passwordConfirmError').text('Mật khẩu và xác nhận mật khẩu không khớp.');
            hasError = true;
        }

        return hasError;
    }


    $(document).ready(function () {
        if (!['/login', '/register', '/', '/loginOtp', '/otpForm'].includes(window.location.pathname)) {
            fetchUser();
        }
    });

    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');

        const isPassword = passwordInput.type === 'password';
        passwordInput.type = isPassword ? 'text' : 'password';

        icon.classList.toggle('bi-eye-fill', !isPassword);
        icon.classList.toggle('bi-eye-slash-fill', isPassword);
    }

    document.addEventListener('DOMContentLoaded', function () {
        const otpInputs = document.querySelectorAll('.otp-input');


        otpInputs.forEach((input, index) => {
            input.addEventListener('input', function (e) {
                if (this.value.length === 1 && index < 5) {
                    otpInputs[index + 1].focus();
                }
            });

            input.addEventListener('keydown', function (e) {
                if (e.key === 'Backspace' && !this.value && index > 0) {
                    otpInputs[index - 1].focus();
                }
            });

            const otp = Array.from(otpInputs).map(i => i.value).join('');
            if (otp.length === 6) {
                verifyOtp();
            }
        });
    });

    function verifyOtp() {
        const inputs = document.querySelectorAll('.otp-input');
        const otp = Array.from(inputs).map(i => i.value).join('');

        if (otp.length !== 6) {
            Swal.fire({
                icon: 'error',
                title: 'Lỗi',
                text: 'Vui lòng nhập đủ OTP'
            });
            return;
        }

        inputs.forEach(input => input.disabled = true);

        $.ajax({
            url: '{{ url("api/auth/otp/verify") }}',
            method: 'POST',
            data: {
                phone: currentPhone,
                otp_code: otp
            },
            success: function (response) {
                Swal.fire({
                    icon: 'success',
                    title: 'Đăng nhập thành công',
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = '/bookings';
                });

            },
            error: function (xhr) {
                let message = 'Có lỗi xảy ra, vui lòng thử lại';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    message = xhr.responseJSON.message;
                }

                Swal.fire({
                    icon: 'error',
                    title: 'Đăng nhập thất bại',
                    timer: 1500,
                    showConfirmButton: false
                });

                // Enable lại inputs
                inputs.forEach(input => {
                    input.disabled = false;
                });
                inputs[0].focus();
            }
        });
    }


</script>