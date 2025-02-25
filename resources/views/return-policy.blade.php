<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>นโยบายการคืนสินค้า</title>
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
</head>
<body>
    @include('navbar')

    <div class="main-wrapper">
        <div class="container">

            <div class="section topup-section" style="text-align:left;">
                <h1>นโยบายการคืนสินค้า</h1>
                <span>
                    ขอบคุณที่เลือกซื้อสินค้าดิจิตอลจากเว็บไซต์ของเรา (<a href="https://redpotionth.com" target="_blank" class="footerlink">redpotionth.com</a>) ทางบริษัทฯ มีความมุ่งมั่นในการให้บริการสินค้าที่มีคุณภาพและตอบสนองความต้องการของลูกค้าอย่างดีที่สุด
                </span>

                <h3>1. ข้อกำหนดเบื้องต้น</h3>
                <span>
                    สินค้าที่ บริษัทฯ จัดจำหน่ายบนเว็บไซด์ (<a href="https://redpotionth.com" target="_blank" class="footerlink">redpotionth.com</a>) เป็นสินค้าดิจิตอลที่ไม่สามารถคืนสินค้า และไม่สามารถคืนเงินได้ เนื่องจากสินค้าดิจิตอลไม่สามารถนำกลับมาใช้ใหม่ได้หลังจากที่ลูกค้ารับแล้ว
                </span>

                <h3>2. เงื่อนไขและข้อยกเว้น</h3>
                <ul>
                    <li>หลังจากการสั่งซื้อและชำระเงินเสร็จสิ้น ลูกค้าจะได้รับสิทธิ์ในการเข้าถึงสินค้าดิจิตอลทันที</li>
                    <li>บริษัทฯ ไม่มีนโยบายคืนสินค้าหรือคืนเงินในทุกกรณี ยกเว้นกรณีที่เกิดข้อผิดพลาดจากทางบริษัทฯ อย่างชัดแจ้ง</li>
                    <li>หากลูกค้าประสบปัญหาในเข้าถึงสินค้าดิจิตอล ควรติดต่อฝ่ายบริการลูกค้าเพื่อตรวจสอบและแก้ไขปัญหา</li>
                    <li>ในกรณีที่เกิดการชำระเงินซ้ำหรือข้อผิดพลาดทางเทคนิค ทางบริษัทฯ จะดำเนินการตรวจสอบและคืนเงินในกรณีที่มีหลักฐานยืนยันได้อย่างชัดเจน</li>
                </ul>

                <h3>3. ขั้นตอนการร้องเรียนและติดต่อสอบถาม</h3>
                <span>
                    หากท่านมีข้อร้องเรียน ปัญหา หรือข้อสงสัยเกี่ยวกับการสั่งซื้อสินค้าดิจิตอล สามารถติดต่อได้ตามช่องทางด้านล่าง:
                </span>
                <ul>
                    <li>อีเมล: redpotionth@gmail.com</li>
                    <li>Line: <a href="https://lin.ee/tHJwLONc" target="_blank" class="footerlink">@redpotionth</a></li>
                    <li>Facebook inbox: <a href="https://www.facebook.com/redpotiontopup" target="_blank" class="footerlink">Red Potion</a></li>
                </ul>

                <h3>4. การแก้ไขและปรับปรุงนโยบาย</h3>
                <span>
                    บริษัทฯ ขอสงวนสิทธิ์ในการแก้ไขหรือปรับปรุงนโยบายการคืนสินค้าโดยไม่ต้องแจ้งให้ทราบล่วงหน้า การเปลี่ยนแปลงจะมีผลบังคับใช้ตั้งแต่วันที่ประกาศบนเว็บไซต์ โปรดตรวจสอบนโยบายนี้เป็นระยะเพื่อรับทราบข้อมูลล่าสุด
                </span>

                <h3>5. ข้อจำกัดความรับผิดชอบ</h3>
                <span>
                    บริษัทฯ จะไม่รับผิดชอบต่อความเสียหายหรือปัญหาที่อาจเกิดขึ้นจากการใช้สินค้าดิจิตอลหลังจากที่ลูกค้าได้เปิดใช้งานแล้ว ทางบริษัทฯ จะพยายามแก้ไขปัญหาที่เกิดขึ้นโดยเร็วที่สุด แต่ไม่สามารถรับประกันได้ในทุกกรณี
                </span>

                <h3>6. การยอมรับนโยบาย</h3>
                <span>
                    การสั่งซื้อสินค้าจากเว็บไซต์ (<a href="https://redpotionth.com" target="_blank" class="footerlink">redpotionth.com</a>) ถือว่าท่านได้อ่านและยอมรับนโยบายการคืนสินค้านี้แล้ว
                </span>

                <div><em><br>วันที่ปรับปรุงล่าสุด: 25 กุมภาพันธ์ 2568</em></div>                
            </div>
        </div>
    </div>


    <script>
        function toggleMenu() {
            const menu = document.getElementById("navbarMenu");
            menu.classList.toggle("show");
        }
    </script>
    @include('footer')
</body>
</html>                