<script>
$(document).ready(function () {
    const serviceSelect = document.getElementById('serviceSelect');
    const durationText = document.getElementById('durationText');
    const noteToggle = document.getElementById('noteToggle');
    const noteGroup = document.getElementById('noteGroup');
    const timeSlotsContainer = document.getElementById('timeSlots');

    // Toggle ghi chú
    noteToggle.addEventListener('change', () => {
        noteGroup.style.display = noteToggle.checked ? 'block' : 'none';
    });

    // Load dịch vụ
    $.ajax({
        url:'api/services/getAll',
        method: 'GET',
        success: function(res){
            if (res.success && res.data.length > 0) {
                res.data.forEach(service => {
                    const option = document.createElement('option');
                    option.value = service.id;
                    option.textContent = `${service.service_name}`;
                    option.dataset.duration = service.duration;
                    serviceSelect.appendChild(option);
                })
            }
        }
    });

    // Hiển thị thời gian thực hiện
    serviceSelect.addEventListener('change', function () {
        const selected = this.options[this.selectedIndex];
        const duration = selected.dataset.duration;
        if (duration) {
            durationText.style.display = 'block';
            durationText.textContent = `Thời gian thực hiện: ${duration} phút.`;
        } else {
            durationText.style.display = 'none';
        }
    });

    // Generate khung giờ (9:00 - 20:00)
    const generateTimeSlots = () => {
        const selectedDate = document.getElementById('bookingDate').value;
        console.log(selectedDate);

        $.ajax({
            url:'api/timeslots/generate-slot',
            method:'POST',
            dataType: 'json',
            data:{
                date: selectedDate
            },
            success: function(res){
                if (res.success && res.data.length > 0) {
                    timeSlotsContainer.innerHTML = '';
                    res.data.forEach(slot => {
                        const col = document.createElement('div');
                        col.className = 'col-3 mb-2';
                        col.innerHTML = `
                            <button type="button" class="btn btn-outline-secondary w-100 time-btn" ${slot.disabled ? 'disabled' : ''}>
                                ${slot.time}
                            </button>
                           `;
                        timeSlotsContainer.appendChild(col);

                    });
                }else{
                    timeSlotsContainer.innerHTML = '<div class="text-center text-danger">Không có khung giờ khả dụng.</div>';
                }
            }
        });
              
        // Chọn giờ
        document.querySelectorAll('.time-btn').forEach(btn => {
            btn.addEventListener('click', function () {
                document.querySelectorAll('.time-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
            });
        });
    };

    document.getElementById('bookingDate').addEventListener('change', generateTimeSlots);
    generateTimeSlots(); // init lần đầu
});
</script>