<?php
session_start();
require_once '../config.php';
$bodyClass = 'map-page';
include "../layout/header_public.php";
?>

<div class="container my-5">
    <div class="text-center mb-5">
        <h1 class="display-5 fw-bold">HỆ THỐNG CỬA HÀNG</h1>
        <p class="lead text-muted">Tìm cửa hàng gần bạn</p>
    </div>

    <div class="filter-section bg-light p-4 rounded-4 shadow-sm mb-5">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tỉnh/Thành phố</label>
                <select id="cityFilter" class="form-select">
                    <option value="">Tất cả</option>
                    <option value="Hà Nội">Hà Nội</option>
                    <option value="Đà Nẵng">Đà Nẵng</option>
                    <option value="Hồ Chí Minh">Hồ Chí Minh</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Quận/Huyện</label>
                <select id="districtFilter" class="form-select">
                    <option value="">Tất cả</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Tìm theo tên cửa hàng</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Nhập tên cửa hàng...">
            </div>
        </div>
    </div>

    <div class="row g-4" id="storesContainer">
    </div>
</div>

<script>
const stores = [
    {
        id: 1,
        name: "Số 10 Đội Cấn",
        address: "Số 10 Đội Cấn, Phường Ba Đình, Hà Nội",
        phone: "0243 823 2351",
        hours: "8:30 - 21:00",
        city: "Hà Nội",
        district: "Ba Đình",
        lat: 21.0365,
        lng: 105.8342
    },
    {
        id: 2,
        name: "Số 276 Nguyễn Văn Linh",
        address: "Số 276 Nguyễn Văn Linh, Phường Thanh Khê, Đà Nẵng",
        phone: "0236 374 7068",
        hours: "8:30 - 21:00",
        city: "Đà Nẵng",
        district: "Thanh Khê",
        lat: 16.0544,
        lng: 108.2022
    },
    {
        id: 3,
        name: "Aura Jewelry - 02 Võ Oanh",
        address: "02 Võ Oanh, Phường 25, Quận Bình Thạnh, Hồ Chí Minh",
        phone: "(028) 1234 5678",
        hours: "9:00 - 21:00",
        city: "Hồ Chí Minh",
        district: "Bình Thạnh",
        lat: 10.8045,
        lng: 106.7122
    }
];

const districtsByCity = {
    "Hà Nội": ["Ba Đình", "Hoàn Kiếm", "Đống Đa", "Cầu Giấy", "Hai Bà Trưng"],
    "Đà Nẵng": ["Thanh Khê", "Hải Châu", "Sơn Trà", "Ngũ Hành Sơn", "Liên Chiểu"],
    "Hồ Chí Minh": ["Bình Thạnh", "Quận 1", "Quận 3", "Quận 7", "Tân Bình", "Gò Vấp"]
};

function updateDistricts() {
    const city = document.getElementById("cityFilter").value;
    const districtSelect = document.getElementById("districtFilter");
    districtSelect.innerHTML = '<option value="">Tất cả</option>';
    if (city && districtsByCity[city]) {
        districtsByCity[city].forEach(district => {
            const option = document.createElement("option");
            option.value = district;
            option.textContent = district;
            districtSelect.appendChild(option);
        });
    }
}

function renderStores() {
    const city = document.getElementById("cityFilter").value;
    const district = document.getElementById("districtFilter").value;
    const keyword = document.getElementById("searchInput").value.toLowerCase();
    const container = document.getElementById("storesContainer");
    
    const filtered = stores.filter(store => {
        if (city && store.city !== city) return false;
        if (district && store.district !== district) return false;
        if (keyword && !store.name.toLowerCase().includes(keyword) && !store.address.toLowerCase().includes(keyword)) return false;
        return true;
    });
    
    if (filtered.length === 0) {
        container.innerHTML = `<div class="col-12 text-center py-5"><p class="text-muted">Không tìm thấy cửa hàng nào.</p></div>`;
        return;
    }
    
    container.innerHTML = filtered.map(store => `
        <div class="col-md-6 col-lg-4">
            <div class="store-card h-100 border-0 shadow-sm rounded-4 p-4 bg-white">
                <h4 class="fw-bold mb-3">${store.name}</h4>
                <p class="mb-2"><i class="bi bi-geo-alt-fill text-gold me-2"></i> ${store.address}</p>
                <p class="mb-2"><i class="bi bi-telephone-fill text-gold me-2"></i> ${store.phone}</p>
                <p class="mb-3"><i class="bi bi-clock-fill text-gold me-2"></i> ${store.hours}</p>
                <a href="https://www.google.com/maps/search/?api=1&query=${store.lat},${store.lng}" target="_blank" class="btn btn-outline-gold rounded-pill px-4">
                    <i class="bi bi-map me-1"></i> Xem bản đồ
                </a>
            </div>
        </div>
    `).join('');
}

document.getElementById("cityFilter").addEventListener("change", () => {
    updateDistricts();
    renderStores();
});
document.getElementById("districtFilter").addEventListener("change", renderStores);
document.getElementById("searchInput").addEventListener("input", renderStores);

updateDistricts();
renderStores();
</script>
<?php include "../layout/footer_public.php"; ?>