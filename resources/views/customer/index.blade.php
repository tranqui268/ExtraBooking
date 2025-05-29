@extends('layouts.admin')

@section('content')
    <div class="p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <select class="form-select" id="orderFilter">
                    <option selected>All Orders</option>
                    <option>High Value</option>
                    <option>New Customers</option>
                </select>
            </div>
            <div>
                <button class="btn btn-outline-secondary">
                    Import
                    <input type="file" id="importFile" name="file" accept=".xlsx" hidden>
                </button>
                <button class="btn btn-outline-secondary">Export</button>
            </div>
        </div>

        <form id="searchForm" method="get" class="form-inline mb-2">
            <input id="searchName" type="text" name="name" placeholder="Nhập họ tên" class="form-control mr-1" />
            <input id="searchEmail" type="text" name="email" placeholder="Nhập email" class="form-control mr-1" />

            <select id="searchStatus" name="status" class="form-control mr-1">
                <option value="">Chọn trạng thái</option>
                <option value="1">Đang hoạt động</option>
                <option value="0">Tạm khóa</option>
            </select>

            <input id="searchAddress" type="text" name="email" placeholder="Nhập địa chỉ" class="form-control mr-1" />

            <button type="submit" class="btn btn-primary"><i class="bi bi-search"></i> Tìm kiếm</button>
            <a href="#" id="clearSearchBtn" class="btn btn-secondary ml-1"><i class="bi bi-x-circle"></i> Xóa tìm</a>
        </form>

        <table id="customerTable" class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th scope="col">Customer name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Phone</th>
                    <th scope="col">Address</th>
                    <th scope="col">Orders</th>
                    <th scope="col">Spent</th>
                    <th></th>
                </tr>
            </thead>
            <tbody id="customerTableBody">
                
            </tbody>
        </table>

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div>
                Show result:
                <select id="pageSize" class="form-select d-inline-block w-auto ms-2">
                    <option value="6" selected>6</option>
                    <option value="10" >10</option>
                    <option value="20" >20</option>
                </select>
            </div>
            <div>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center" id="pagination">
                    </ul>
                </nav>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    @include('scripts.customer-scripts');
@endsection