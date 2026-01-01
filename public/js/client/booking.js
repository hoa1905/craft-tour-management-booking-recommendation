document.addEventListener('DOMContentLoaded', function () {
	const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
	const adultInput = document.querySelector('.adult_quantity');
	const childInput = document.querySelector('.child_quantity');
	const totalPriceEl = document.getElementById('ext_total_price');
	const priceAdult = parseInt(document.querySelector('.i-price').dataset.price);
	const priceChild = parseInt(document.querySelector('.i-price_child').dataset.price);
	// Lấy giá trị số chỗ còn lại
	const seatsAvailableInput = document.getElementById('seats_available');
	const seatsAvailable = seatsAvailableInput ? parseInt(seatsAvailableInput.value) : 999;

	// Lấy các input hidden
	const hiddenAdultInput = document.querySelector('.adult_quantity_hidden');
	const hiddenChildInput = document.querySelector('.child_quantity_hidden');
	const hiddenTotalPriceInput = document.getElementById('hidden_total_price');

	const btnOffice = document.getElementById('btnOffice');
	const btnPaypal = document.getElementById('btnPaypal');
	const btnVnpay = document.getElementById('btnVnpay');
	const paymentRadios = document.querySelectorAll('input[name="payment_method"]');

	// Hàm tính tổng tiền
	function updateTotal() {
		const adult = parseInt(adultInput.value) || 1;
		const child = parseInt(childInput.value) || 0;
		const total = (adult * priceAdult) + (child * priceChild);
		totalPriceEl.textContent = total.toLocaleString('vi-VN') + '₫';

		// Thêm cập nhật cho các trường input hidden
		hiddenAdultInput.value = adult;
		hiddenChildInput.value = child;
		hiddenTotalPriceInput.value = total;
	}

	// Hàm kiểm tra giới hạn chỗ
	// Trả về false nếu vượt quá giới hạn
	function checkSeatLimit(newAdultQty, newChildQty) {
		const totalGuests = newAdultQty + newChildQty;
		if (totalGuests > seatsAvailable) {
			alert(`Ngày khởi hành này chỉ còn nhận ${seatsAvailable} khách!`);
			return false;
		}
		return true;
	}

	// Giới hạn chỉ nhập số & xử lý khi nhập tay
	function validateInput(input, minValue = 0) {
		input.addEventListener('change', function () {
			// 1. Validate số cơ bản
			let value = parseInt(this.value) || minValue;
			if (value < minValue) value = minValue;

			// 2. Lấy giá trị hiện tại của ô kia để tính tổng
			const currentAdult = parseInt(adultInput.value) || 0;
			const currentChild = parseInt(childInput.value) || 0;

			let proposedAdult, proposedChild;

			if (this.classList.contains('adult_quantity')) {
				proposedAdult = value;
				proposedChild = currentChild;
			} else {
				proposedAdult = currentAdult;
				proposedChild = value;
			}

			// 3. Kiểm tra giới hạn
			if (!checkSeatLimit(proposedAdult, proposedChild)) {
				// Nếu vượt quá, tính toán số lượng tối đa có thể nhập cho ô này
				const otherQty = this.classList.contains('adult_quantity') ? currentChild : currentAdult;
				let maxAllowed = seatsAvailable - otherQty;
				if (maxAllowed < minValue) maxAllowed = minValue;

				this.value = maxAllowed; // Reset về giá trị tối đa cho phép
			} else {
				this.value = value;
			}

			// Riêng người lớn không được < 1 (trừ khi hết chỗ hoàn toàn thì xử lý server)
			if (this.classList.contains('adult_quantity') && parseInt(this.value) < 1) {
				this.value = 1;
			}

			updateTotal();
		});

		// Chặn nhập ký tự chữ
		input.addEventListener('input', function () {
			this.value = this.value.replace(/[^0-9]/g, '');
		});
	}

	// Nút tăng giảm
	function setupQuantityButtons(container, input, minValue = 0) {
		const btnDown = container.querySelector('.btn-down');
		const btnUp = container.querySelector('.btn-up');

		btnUp.addEventListener('click', function () {
			let value = parseInt(input.value) || minValue;
			const nextValue = value + 1;

			// Tính tổng dự kiến
			const currentAdult = parseInt(adultInput.value) || 0;
			const currentChild = parseInt(childInput.value) || 0;

			let proposedAdult, proposedChild;

			if (input.classList.contains('adult_quantity')) {
				proposedAdult = nextValue;
				proposedChild = currentChild;
			} else {
				proposedAdult = currentAdult;
				proposedChild = nextValue;
			}

			// Kiểm tra xem tăng lên có vượt quá không
			if (checkSeatLimit(proposedAdult, proposedChild)) {
				input.value = nextValue;
				updateTotal();
			}
		});

		btnDown.addEventListener('click', function () {
			let value = parseInt(input.value) || minValue;
			value = Math.max(value - 1, minValue);
			// số lượng người lớn phải >= 1
			if (input.classList.contains('adult_quantity') && value < 1) value = 1;

			input.value = value;
			updateTotal();
		});
	}

	validateInput(adultInput, 1);
	validateInput(childInput, 0);
	setupQuantityButtons(document.querySelector('.q-adult'), adultInput, 1);
	setupQuantityButtons(childInput.closest('.iquan'), childInput, 0);
	updateTotal();

	// Ẩn/hiện nút thanh toán theo phương thức được chọn
	paymentRadios.forEach(radio => {
		radio.addEventListener('change', function () {
			btnOffice.classList.add('hidden');
			btnPaypal.classList.add('hidden');
			btnVnpay.classList.add('hidden');

			if (this.value === 'office') btnOffice.classList.remove('hidden');
			if (this.value === 'paypal') btnPaypal.classList.remove('hidden');
			if (this.value === 'vnpay') btnVnpay.classList.remove('hidden');
		});
	});

	// Kiểm tra form trước khi gửi
	function validateForm() {
		// 1. Kiểm tra số chỗ lần cuối (đề phòng hack html)
		const adult = parseInt(adultInput.value) || 0;
		const child = parseInt(childInput.value) || 0;
		if (!checkSeatLimit(adult, child)) {
			return false;
		}

		let valid = true;
		const requiredFields = ['full_name', 'phone', 'email', 'address'];

		// Kiểm tra các trường bắt buộc (required)
		requiredFields.forEach(id => {
			const field = document.getElementById(id);
			if (!field.value.trim()) {
				field.classList.add('is-invalid');
				valid = false;
			} else {
				field.classList.remove('is-invalid');
			}
		});

		// Nếu có trường bắt buộc bị trống
		if (!valid) {
			toastr.error('Vui lòng nhập đầy đủ thông tin bắt buộc trước khi thanh toán!');
			return false;
		}

		// Nếu tất cả đã được điền, kiểm tra logic riêng cho SĐT
		const phone = document.getElementById('phone');

		// Kiểm tra xem có đúng 10 số không
		if (!/^\d{10}$/.test(phone.value)) {
			phone.classList.add('is-invalid');
			toastr.error('Số điện thoại phải gồm đúng 10 chữ số!');
			return false;
		}

		return true;
	}

	// Gửi dữ liệu từ form đến server
	// dữ liệu từ các trường input (cả hidden và visible)
	async function submitPayment(url, successMessage) {
		const formData = new FormData(document.getElementById('fBooking'));
		try {
			const res = await fetch(url, {
				method: 'POST',
				headers: {
					'X-CSRF-TOKEN': csrfToken,
					'Accept': 'application/json'
				},
				body: formData
			});
			const data = await res.json();
			if (data.success) {
				if (data.redirect_url) {
					// Nếu là PayPal/Vnpay, chuyển hướng
					window.location.href = data.redirect_url;
				} else {
					// Nếu là thanh toán tại văn phòng
					toastr.success(data.message || successMessage);
					// Thành công chuyển hướng đến trang quản lý đơn
					window.location.href = '/account/my-bookings';
				}
			} else {
				toastr.error(data.message || 'Có lỗi xảy ra!');
			}
		} catch (error) {
			toastr.error('Không thể gửi yêu cầu. Vui lòng thử lại!');
			console.error(error);
		}
	}

	// Gắn sự kiện click cho nút thanh toán
	btnOffice.addEventListener('click', function () {
		if (!validateForm()) return;
		submitPayment('/payment/office', 'Đặt tour thành công tại văn phòng!');
	});

	btnPaypal.addEventListener('click', function () {
		if (!validateForm()) return;
		submitPayment('/payment/paypal', 'Đang chuyển hướng đến PayPal Sandbox...');
	});

	btnVnpay.addEventListener('click', function () {
		if (!validateForm()) return;
		submitPayment('/payment/vnpay', 'Đang chuyển hướng đến VNPAY Sandbox...');
	});
});
