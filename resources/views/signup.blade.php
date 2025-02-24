<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body>
    <div class="main-wrapper">
        <div class="login-container">
            <div class="login-logo">
                <a href="{{ url('/') }}">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo" class="logo-image">
                </a>
            </div> 
            <h1 class="login-title">สมัครสมาชิก</h1>
            <p class="login-subtitle">กรุณากรอกข้อมูลเพื่อสมัครสมาชิก</p>
            
            @if ($errors->any())
                <div class="signupalert">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif            

            <form action="{{ route('custom.signup') }}" method="POST" class="login-form" onsubmit="return validateTerms()">
                @csrf
                <div class="form-group">
                    <label for="username" class="form-label">ชื่อผู้ใช้</label>
                    <input type="text" id="username" name="username" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="email" class="form-label">อีเมล</label>
                    <input type="email" id="email" name="email" class="form-input" required>
                </div>
                <div class="form-group">
                    <label for="password" class="form-label">รหัสผ่าน</label>
                    <input type="password" id="password" name="password" class="form-input" required>
                </div>

                <div class="terms-container register-link">
                    <input type="checkbox" id="terms" name="terms" required>
                    <label for="terms">
                        ฉันยอมรับ 
                        <a href="#" onclick="openTermsModal()">ข้อตกลงและเงื่อนไข</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">สมัครสมาชิก</button>
            </form>

            <div class="register-link">
                <p>มีสมาชิกอยู่แล้ว? <a href="{{ route('custom.login.form') }}">เข้าสู่ระบบ</a></p>
            </div>
        </div>
    </div>

    <div id="termsModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeTermsModal()">&times;</span>
            <h2>ข้อตกลงและเงื่อนไขการใช้บริการ</h2>
            <p>เมื่อทำการสมัครสมาชิกกับ Red Potion คุณตกลงและยอมรับเงื่อนไขต่อไปนี้:</p>
            <ul>
                <li><strong>การใช้บริการ:</strong> ผู้ใช้ต้องให้ข้อมูลที่เป็นจริง และห้ามใช้บัญชีเพื่อกระทำการผิดกฎหมาย</li>
                <li><strong>ความเป็นส่วนตัว:</strong> ข้อมูลส่วนตัวของคุณจะถูกเก็บรักษาอย่างปลอดภัย</li>
                <li><strong>การชำระเงิน:</strong> สินค้าและบริการที่ซื้อแล้วจะไม่สามารถขอคืนเงินได้</li>
                <li><strong>การเปลี่ยนแปลงเงื่อนไข:</strong> บริษัทมีสิทธิ์ในการเปลี่ยนแปลงข้อตกลงโดยไม่ต้องแจ้งล่วงหน้า</li>
            </ul>
            <p>กรุณาอ่านและทำความเข้าใจก่อนสมัครสมาชิก</p>
        </div>
    </div>

    <script>
        function validateTerms() {
            let termsCheckbox = document.getElementById("terms");
            if (!termsCheckbox.checked) {
                alert("กรุณายอมรับข้อตกลงและเงื่อนไขก่อนสมัครสมาชิก");
                return false;
            }
            return true;
        }

        function openTermsModal() {
            document.getElementById("termsModal").style.display = "block";
        }

        function closeTermsModal() {
            document.getElementById("termsModal").style.display = "none";
        }

        window.onclick = function(event) {
            let modal = document.getElementById("termsModal");
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
