@extends('client.master')
@section('title', 'Trang chi tiết tour')
@section('body')
<div id="vnt-content">
  <div id="vnt-navation" class="breadcrumb">
    <div class="wrapper">
      <div class="navation">
        <ul>
          <li><a href="http://127.0.0.1:8000/">Tour du lịch</a></li>
          <li><a href="">{{ $tour->village->region }}</a></li>
          <li class="hidden-xs"><a
              href="">{{ $tour->tour_name }}</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="box_mid">
    <div class="wrapper">
      <div class="mid-title">
        <h1 class="title-main">{{ $tour->tour_name }}</h1>
      </div>
      <div class="mid-content">
        <div class="wrap-scroll">
          <div class="row cel-p10">
            <div class="col-md-4 col-sm-12 col-xs-12 pull-right">
              <div class="box-detail-tour">
                <table class="table-detail">
                  <tbody>
                    <tr>
                      <td>
                        <div class="i-label fa-qrcode">Mã tour:</div>
                      </td>
                      <td><span class="color1">{{ $tour->tour_id }}</span></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="i-label fa-map-marker-alt">Điểm đi:</div>
                      </td>
                      <td><a href="/tour/tour.html/?departure=1" target="_blank">{{ $tour->departure_point }}</a>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="i-label fa-route"><strong>Điểm Đến:</strong></div>
                      </td>
                      <td><a href="/tour/mien-trung.html" target="_blank">{{ $tour->destination }}</a></td>
                    </tr>
                    <tr>
                      <td>
                        <div class="i-label fa-map-marked-alt">Tham quan:</div>
                      </td>
                      <td><a href="/tour/tour.html/?point_id=395" target="_blank">{{ $tour->village->village_name }}</a>,
                        <a href="/tour/tour.html/?point_id=394" target="_blank">Quy Nhơn</a>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div class="i-label fa-clock">Thời gian:</div>
                      </td>
                      <td>{{ $tour->duration }}</td>
                    </tr>
                    <tr>
                      <td>
                        <div class="i-label fa-bus-alt">Phương tiện:</div>
                      </td>
                      <td>{{ $tour->transport }}</td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <div id="advRight" class="hidden-sm hidden-xs">
                <div class="vbackpop"></div>
                <div class="advRight-fixed">
                  <div class="box-detail-tour hasBg">
                    <div class="vpadd">
                      <div class="vtitle">
                        <span>Ngày khởi hành và giá tour</span>
                      </div>
                      <div class="vconts boxCart">
                        <div class="ctcheck">
                          <div class="ckgrip">
                            <div class="xcol">
                              <div class="ckchose clchoose">
                                <ul>
                                  <li class="active" data-date="13/03/2026">13/03
                                  </li>
                                  <li data-date="17/03/2026">17/03</li>
                                  <li data-date="25/03/2026">25/03</li>
                                </ul>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="ctchose">
                          <div class="csitem">
                            <div class="iigrip">
                              <div class="xcol">
                                <div class="itext">
                                  <div class="tm">Người lớn</div>
                                  <div class="th">>10 tuổi</div>
                                </div>
                              </div>
                              <div class="ycol">
                                <div class="inumb">
                                  <div class="ncore">X</div>
                                  <div class="ninfo">
                                    <div class="sold i-price_old"></div>
                                    <div class="snew i-price">{{ number_format($tour->price, 0, ',', '.') }} đ</div>
                                  </div>
                                </div>
                              </div>
                              <div class="zcol">
                                <div class="iquan q-adult">
                                  <button type="button" class="btn-down"> <i
                                      class="fa fa-minus"></i></button>
                                  <input type="text"
                                    class="ipt-qua adult_quantity" value="1">
                                  <button type="button" class="btn-up"><i
                                      class="fa fa-plus"></i></button>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="csitem">
                            <div class="iigrip">
                              <div class="xcol">
                                <div class="itext">
                                  <div class="tm">Trẻ em</div>
                                  <div class="th">2 - 10 tuổi</div>
                                </div>
                              </div>
                              <div class="ycol">
                                <div class="inumb">
                                  <div class="ncore">X</div>
                                  <div class="ninfo">
                                    <div class="sold i-price_old_child"></div>
                                    <div class="snew i-price_child">5,490,000 đ
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="zcol">
                                <div class="iquan">
                                  <button type="button" class="btn-down"> <i
                                      class="fa fa-minus"></i></button>
                                  <input type="text"
                                    class="ipt-qua child_quantity" value="0">
                                  <button type="button" class="btn-up"><i
                                      class="fa fa-plus"></i></button>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="ctprice">
                          <div class="prgrip">
                            <div class="xcol">
                              <div class="polds total-price-old"></div>
                              <div class="pnews total-price">{{ number_format($tour->price, 0, ',', '.') }} đ</div>
                            </div>
                            <div class="vcol">
                              <div class="ctlinks">
                                <a href="javascript:void(0)"
                                  class="book-tour addCart" rel="nofollow"
                                  data-id="191">
                                  <i class="fa fa-shopping-cart"></i> Đặt tour
                                </a>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="ctinfos">
                          <div class="ifgrip">
                            <div class="xcol">
                              <div class="iicon">
                                <img src="{{ asset('images/ihotline.png') }}" alt="ihotline">
                              </div>
                            </div>
                            <div class="vcol">
                              <div class="itext">
                                <div class="tm">Tổng đài tư vấn, đặt tour:</div>
                                <div class="th"><span style="color: #d82727;">0123 456 789</span></div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="div_adv">

                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-8 col-sm-12 col-xs-12">
              <div class="vhupgradenew">
                <div class="tpupgradenm">
                  <div class="nmgrip">
                    <div class="lcol">
                      <div class="nmponsi">
                        <div class="imupgradenm">
                          <div class="slupgradenm">
                            <div class="colsl">
                              <div class="itupgradenma clpopalbum">
                                <img src="{{ asset('images/banner-tour-detail-1.jpg') }}"
                                  alt="">
                              </div>
                            </div>
                            <div class="colsl">
                              <div class="itupgradenma clpopalbum">
                                <img src="{{ asset('images/banner-tour-detail-2.jpg') }}"
                                  alt="">
                              </div>
                            </div>

                            <div class="colsl">
                              <div class="itupgradenma clpopalbum">
                                <img src="{{ asset('images/banner-tour-detail-3.jpg') }}"
                                  alt="">
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="tab_wrapper tab_responsive">
                <div class="viewtab vhtabtour" id="viewtabDesc">
                  <div class="vvtitle">
                    <div class="icon"><i class="fa fa-paperclip"></i></div>
                    <div class="txt">
                      <h2>Giới thiệu tổng quan về tour</h2>
                    </div>
                  </div>
                  <div class="vvcont">
                    <div class="the-content ">
                      <div class="box-info">
                        <div class="info-content">
                          <div class="hpviewcont">
                            <div class="thecontent desc">
                              <div class="txt-intro">
                                <p style="text-align: justify;">{{ $tour->description }}</p>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="viewtab vhtabtour" id="viewtabProgram">
                  <div class="vvtitle vchange">
                    <div class="xxgrip">
                      <div class="xcol">
                        <div class="tname">
                          <div class="icon"><i class="fa fa-info-circle"></i></div>
                          <div class="txt"><b> LỊCH TRÌNH TOUR CHI TIẾT</b></div>
                        </div>
                      </div>
                      <div class="ycol">
                        <div class="tview">
                          <a class="clviewsmore" href="javascript:void(0);"><span
                              class="vview">Xem toàn bộ</span> <span class="xhide">Thu
                              gọn</span></a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="vvcont">
                    <div class="ltschedule">

                      <div class="itschedule active">
                        <div class="sctitle">
                          NGÀY 01: HÀ NỘI - TUY HÒA (ĂN TRƯA NHẸ, TỐI)
                        </div>
                        <div class="scconts">
                          <div class="desc">
                            <p style="text-align: justify;"><strong>09h00:�</strong>Xe
                              và Hướng dẫn viên đón Quý khách tại Công viên Thống Nhất
                              -�đường Trần Nhân Tông, khởi hành ra sân bay Nội Bài làm
                              thủ tục đáp chuyến bay<strong>�VN1652 lúc 11h50 đi Tuy
                                Hòa.�</strong></p>

                            <p style="text-align: justify;"><strong>13h35:�</strong>Tới
                              Tuy Hòa, đoàn ăn trưa nhẹ đặc sản. Xe đưa đoàn đi thăm
                              quan:�</p>

                            <ul>
                              <li style="text-align: justify;"><strong>Vịnh Vũng Rô
                                  -</strong>�một trong những vịnh đẹp nổi tiếng
                                của khu vực ven biển Miền Trung, nằm tiếp giáp với
                                Vịnh Vân Phong, nơi cập bến của những chuyến tàu
                                không số với huyền thoại đường Hồ Chí Minh trên
                                biển.�</li>
                              <li style="text-align: justify;"><strong>Hải Đăng Mũi
                                  Điện</strong>, giao lưu nhân viên trực Hải Đăng,
                                chụp hình tại cột mốc điểm cực đông, ngắm nhìn toàn
                                cảnh trên độ cao 110m so với mặt nước biển.�</li>
                            </ul>

                            <p style="text-align: justify;">Đoàn về nhận phòng khách
                              sạn, nghỉ ngơi, tự do tắm biển Tuy Hòa.�</p>

                            <p style="text-align: justify;"><strong>Tối:�</strong>Quý
                              khách ăn tối tại nhà hàng. Nghỉ đêm tại khách sạn Tuy
                              Hòa.</p>
                          </div>
                        </div>
                      </div>

                      <div class="itschedule ">
                        <div class="sctitle">
                          NGÀY 02: PHÚ YÊN - QUY NHƠN (ĂN SÁNG, TRƯA, TỐI)
                        </div>
                        <div class="scconts">
                          <div class="desc">
                            <p style="text-align: justify;"><strong>Sáng:</strong>�Quý
                              khách ăn sáng tại khách sạn và trả phòng. Quý khách tham
                              quan:�</p>

                            <ul>
                              <li style="text-align: justify;"><strong>Chùa Thanh
                                  Lương:�</strong>một địa điểm tâm linh nổi tiếng
                                của tỉnh Phú Yên, tọa lạc tại thôn Mỹ Quang Nam của
                                xã An Chấn, huyện Tuy An. Nơi đây nằm cách trung tâm
                                thành phố Tuy Hòa khoảng 10km về phía Bắc.�</li>
                              <li style="text-align: justify;"><strong>Check in Tháp
                                  Nghinh Phong</strong>�-�biểu tượng mới của thành
                                phố Tuy Hoà lấy cảm hứng từ các khối đá ở Ghềnh Đá
                                Đĩa.</li>
                              <li style="text-align: justify;"><strong>Gành Đá
                                  Đĩa</strong>�-�một trong những danh thắng tuyệt
                                vời của Phú Yên được công nhận là Di tích Danh Thắng
                                cấp Quốc Gia, với đặc thù là những cột đá xếp chồng
                                lên nhau giống như những chồng đĩa.�</li>
                              <li style="text-align: justify;"><strong>Nhà Thờ Mằng
                                  Lăng -</strong>�một kiến trúc Pháp Cổ. Tại đây
                                quý khách được chiêm ngưỡng cuốn sách chữ Quốc Ngữ
                                đầu tiên của Việt Nam.</li>
                            </ul>

                            <p style="text-align: justify;"><strong>Trưa:�</strong>Quý
                              khách đến với Đầm Ô Loan, một trong những hệ đầm phá
                              rộng lớn với nhiều hải sản tươi ngon. Quý khách dùng bữa
                              trưa tại nhà hàng nổi trên đầm Ô Loan.</p>

                            <p style="text-align: justify;"><strong>Chiều:�</strong>Đoàn
                              di chuyển quay trở về Quy Nhơn. Tự do tắm biển Quy
                              Nhơn.�</p>

                            <p style="text-align: justify;"><strong>Tối:�</strong>Đoàn
                              dùng bữa tối tại nhà hàng. Sau bữa tối, quý khách tự do
                              thăm quan, dạo chơi quảng trường Thành phố Quy Nhơn,
                              hoặc nhâm nhi tách Café với chút nhạc Trịnh bên bờ biển
                              Quy Nhơn. Nghỉ đêm tại khách sạn 3 sao.</p>
                          </div>
                        </div>
                      </div>

                      <div class="itschedule ">
                        <div class="sctitle">
                          NGÀY 03: KHÁM PHÁ BIỂN ĐẢO QUY NHƠN (ĂN SÁNG, TRƯA)(BỮA TỐI TỰ
                          TÚC)
                        </div>
                        <div class="scconts">
                          <div class="desc">
                            <p style="text-align: justify;"><strong>Sáng:�</strong>Quý
                              khách dùng bữa sáng tại khách sạn.</p>

                            <p style="text-align: justify;"><strong>07h30:�</strong>Xe
                              và HDV Khởi hành đi Nhơn Hải, qua:</p>

                            <ul>
                              <li style="text-align: justify;"><strong>Cầu Thị
                                  Nại�</strong>-�Cây cầu vượt biển với chiều dài
                                gần 2,5km. Quý khách có thể dừng chân, ngắm những
                                tia nắng vàng rắc trên Đầm Thị Nại, nghe kể về những
                                trận thủy chiến oai hùng, bi tráng giữa Champa và
                                Đại Việt, giữa Nhà Tây Sơn và Nhà Nguyễn (Nguyễn
                                Ánh). Quý khách dừng chân tham quan và chụp ảnh lưu
                                niệm Đồi Cát Phương Mai. Đến bến tàu Nhơn Hải, quý
                                khách lên cano đi khám phá KDL đảo Kỳ Co (Giá tour
                                chưa bao gồm chi phí đi Kỳ Co), với làn nước trong
                                vắt, cát trắng, trải nghiệm cảm giác lặn biển ngắm
                                rạn san hô.</li>
                            </ul>

                            <p style="text-align: justify;"><strong>Trưa:�</strong>Đoàn
                              dùng bữa tại nhà hàng trên đất liền.</p>

                            <p style="text-align: justify;"><strong>Chiều:�</strong>đoàn
                              thăm quan các địa danh:</p>

                            <ul>
                              <li style="text-align: justify;"><strong>Danh Thắng Eo
                                  Gió -</strong>�Một ghềnh đá quanh năm lộng gió.
                                Đứng trên Eo Gió, phóng tầm mắt ra xa, quý khách
                                chiêm ngưỡng bao quát cả vùng biển bao la rộng lớn,
                                đắm say lòng người. Eo Gió, được mệnh danh Nơi ngắm
                                hoàng hôn đẹp nhất Việt Nam.</li>
                              <li style="text-align: justify;"><strong>Tịnh Xá Ngọc
                                  Hòa�</strong>-�Chiêm ngưỡng tượng đôi Quan Thế
                                Âm cao khoảng 30m và là nơi trao gửi niềm tin của
                                người dân xã đảo trước những chuyến ra khơi và cầu
                                mong cho chồng con luôn an lành, thuận buồm xuôi
                                gió, mang về những con tàu đầy ắp cá tôm.Xe và hướng
                                dẫn đưa đoàn về trung tâm thành phố.</li>
                              <li style="text-align: justify;">Khu du
                                lịch�<strong>Ghềnh Ráng Tiên Sa -</strong>�dốc Mộng
                                Cầm, đồi Thi Nhân, viếng mộ Hàn Mặc Tử, Bãi tắm
                                Hoàng Hậu. Quý khách đuợc nghe thuyết minh về cuộc
                                đời và sự nghiệp tài hoa của thi sỹ Hàn Mặc Tử.�
                              </li>
                            </ul>

                            <p style="text-align: justify;"><strong>Tối:�</strong>Quý
                              khách tự do ăn tối (chi phí tự túc), khám phá ẩm thực
                              địa phương: các món hải sản, bún cá, bánh canh, bánh
                              xèo, đặc sản gà chỉ… Nghỉ đêm tại khách sạn 3 sao ở Quy
                              Nhơn.</p>
                          </div>
                        </div>
                      </div>

                      <div class="itschedule ">
                        <div class="sctitle">
                          NGÀY 04: QUY NHƠN - HÀ NỘI (ĂN SÁNG)
                        </div>
                        <div class="scconts">
                          <div class="desc">
                            <p style="text-align: justify;"><strong>Sáng</strong>: Đoàn
                              dùng bữa sáng tại nhà hàng. Đoàn tham quan:�</p>

                            <ul>
                              <li style="text-align: justify;"><strong>Tháp
                                  Đôi</strong>�-�cụm tháp với 02 ngọn tháp cổ có
                                lối kiến trúc Chăm, nằm ngay trong lòng thành phố
                                Quy Nhơn.</li>
                              <li style="text-align: justify;"><strong>Tiểu chủng viện
                                  Làng Sông</strong>, di tích gắn liền với sự ra
                                đời của chữ quốc ngữ ở Bình Định vẫn còn nguyên vẹn
                                vẻ đẹp cổ kính hấp dẫn du khách trong nước và quốc
                                tế.</li>
                            </ul>

                            <p style="text-align: justify;">Xe đón đoàn ra sân bay Phù
                              Cát, đáp chuyến bay�<strong>VN1622 khởi hành về lại Hà
                                Nội lúc 13h30.</strong></p>

                            <p style="text-align: justify;">Đến sân bay Nội Bài, xe và
                              HDV đón quý khách về lại trung tâm thành phố. Trả khách
                              tại điểm đón, chia tay bà hẹn gặp lại.</p>

                            <p style="text-align: justify;"><strong>LƯU
                                Ý:�</strong>CHƯƠNG TRÌNH CÓ THỂ THAY ĐỔI ĐỂ PHÙ HỢP
                              VỚI ĐIỀU KIỆN THỰC TẾ, CHÚNG TÔI SẼ SẮP XẾP LẠI LỊCH
                              TRÌNH NHƯNG VẪN ĐẢM BẢO ĐẦY ĐỦ CÁC ĐIỂM THĂM QUAN NHƯ ĐÃ
                              CAM KẾT.</p>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="viewtab vhtabtour" id="viewtabComment">
                  <div class="vvtitle">
                    <div class="icon"><i class="fa fa-comments"></i></div>
                    <div class="txt"><span>Đánh giá tour</span></div>
                  </div>
                  <div class="vvcont">
                    <div class="the-content">
                      <div class="box-info">
                        <div class="info-content">
                          <div class="thecontent">
                            <div class="vnt-review">
                              <div class="itreviewfaq">
                                <div class="rftitle">
                                  <h3>0 Đánh giá về tour</h3>
                                </div>
                                <div class="rfcont">
                                  <div class="box-detail">
                                    <div class="row cel-p10 rowflex">
                                      <div
                                        class="col-md-3 col-sm-12 col-xs-12">
                                        <div class="cricleDG"
                                          data-point="9.5">
                                          <div class="status">Chưa có đánh
                                            giá</div>
                                          <div class="point"></div>
                                          <svg viewBox="0 0 180 180">
                                            <path class="circle"
                                              d="M90 4  a 86 86 0 0 1 0 172  a 86 86 0 0 1 0 -172">
                                            </path>
                                          </svg>
                                        </div>
                                      </div>
                                      <div
                                        class="col-md-6 col-sm-12 col-xs-12">
                                        <div class="row cel-p10">

                                          <div
                                            class="col-md-6 col-sm-6 col-xs-6">
                                            <div class="wrapDG">
                                              <div class="lineDG">
                                                <div>
                                                  <div class="fl">
                                                    Hướng dẫn
                                                    viên</div>
                                                  <div class="fr">
                                                    <span
                                                      class="color1">0</span>
                                                  </div>
                                                  <div
                                                    class="clear">
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="bgLine">
                                                <div class="perLine"
                                                  style="width: 0%;">
                                                </div>
                                              </div>
                                            </div>
                                          </div>

                                          <div
                                            class="col-md-6 col-sm-6 col-xs-6">
                                            <div class="wrapDG">
                                              <div class="lineDG">
                                                <div>
                                                  <div class="fl">
                                                    Lưu trú
                                                  </div>
                                                  <div class="fr">
                                                    <span
                                                      class="color1">0</span>
                                                  </div>
                                                  <div
                                                    class="clear">
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="bgLine">
                                                <div class="perLine"
                                                  style="width: 0%;">
                                                </div>
                                              </div>
                                            </div>
                                          </div>

                                          <div
                                            class="col-md-6 col-sm-6 col-xs-6">
                                            <div class="wrapDG">
                                              <div class="lineDG">
                                                <div>
                                                  <div class="fl">
                                                    Giá cả</div>
                                                  <div class="fr">
                                                    <span
                                                      class="color1">0</span>
                                                  </div>
                                                  <div
                                                    class="clear">
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="bgLine">
                                                <div class="perLine"
                                                  style="width: 0%;">
                                                </div>
                                              </div>
                                            </div>
                                          </div>

                                          <div
                                            class="col-md-6 col-sm-6 col-xs-6">
                                            <div class="wrapDG">
                                              <div class="lineDG">
                                                <div>
                                                  <div class="fl">
                                                    Phương tiện
                                                  </div>
                                                  <div class="fr">
                                                    <span
                                                      class="color1">0</span>
                                                  </div>
                                                  <div
                                                    class="clear">
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="bgLine">
                                                <div class="perLine"
                                                  style="width: 0%;">
                                                </div>
                                              </div>
                                            </div>
                                          </div>

                                          <div
                                            class="col-md-6 col-sm-6 col-xs-6">
                                            <div class="wrapDG">
                                              <div class="lineDG">
                                                <div>
                                                  <div class="fl">
                                                    Địa điểm
                                                  </div>
                                                  <div class="fr">
                                                    <span
                                                      class="color1">0</span>
                                                  </div>
                                                  <div
                                                    class="clear">
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="bgLine">
                                                <div class="perLine"
                                                  style="width: 0%;">
                                                </div>
                                              </div>
                                            </div>
                                          </div>

                                          <div
                                            class="col-md-6 col-sm-6 col-xs-6">
                                            <div class="wrapDG">
                                              <div class="lineDG">
                                                <div>
                                                  <div class="fl">
                                                    Ăn uống
                                                  </div>
                                                  <div class="fr">
                                                    <span
                                                      class="color1">0</span>
                                                  </div>
                                                  <div
                                                    class="clear">
                                                  </div>
                                                </div>
                                              </div>
                                              <div class="bgLine">
                                                <div class="perLine"
                                                  style="width: 0%;">
                                                </div>
                                              </div>
                                            </div>
                                          </div>

                                        </div>
                                      </div>
                                      <div
                                        class="col-md-3 col-sm-12 col-xs-12">
                                        <div class="viewpop">
                                          <a class="popup_edit fancybox.iframe"
                                            href="/tour/tour/popup/reviews.html/?item_id=191">Gửi
                                            đánh giá</a>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>

                            <div class="itreviewfaq">
                              <div class="rftitle">
                                <h3>BÌNH LUẬN</h3>
                              </div>
                              <div class="rfcont">
                                <div class="facebook-comment"
                                  style="padding-top:10px;">
                                  <div class="fb-comments"
                                    data-width="100%" data-num-posts="5"
                                    data-colorscheme="light"></div>
                                  <div id="fb-root"></div>
                                  <script>
                                    (function(d, s, id) {
                                      var js, fjs = d.getElementsByTagName(s)[0];
                                      if (d.getElementById(id)) return;
                                      js = d.createElement(s);
                                      js.id = id;
                                      js.src = "//connect.facebook.net/en_US/all.js#xfbml=1&appId=1305829822896074";
                                      fjs.parentNode.insertBefore(js, fjs);
                                    }(document, "script", "facebook-jssdk"));
                                  </script>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div id="vnt-load_other" class="mb30 vnt-load-tour">
          <div class="style-title-1">
            <div class="fl"><span class="stTitle">Các tour tương tự</span></div>
            <div class="clear"></div>
          </div>
          <div class="vhtourkm loadBottom">
            <div id="mien-nam-tour-list" class="row">
              <div class="col-md-3 col-sm-6">
                <div class="tour-card">
                  <a href="">
                    <div class="tour-image" style="background-image: url('')">
                      <span class="tour-region"><i class="glyphicon glyphicon-map-marker"></i> fake</span>
                    </div>
                  </a>

                  <div class="tour-body">
                    <h4 class="tour-title">
                      <a href="">
                        tour name
                      </a>
                    </h4>

                    <div class="tour-price">
                      1122 <span class="currency">đ</span>
                    </div>

                    <ul class="tour-info list-unstyled">
                      <li><strong>Khởi hành:</strong> 5</li>
                      <li><strong>Điểm đến:</strong> Điểm đến</li>
                      <li><strong>Thời gian:</strong> 7</li>
                    </ul>

                    <div class="clearfix">
                      <div class="pull-left">
                        <strong>Ngày đi:</strong> 1
                      </div>
                      <div class="pull-right">

                        <i class="fa fa-plane"></i>

                        <i class="fa fa-bus"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <!-- Nút xem thêm -->
            <div class="viewall vcenter vfullmb html_nav" style="text-align:center; margin:20px 0;">
              <a href="javascript:void(0)" class="loadmore" data-offset="8" data-region="Tour Miền Nam">
                <span><i class="fa fa-chevron-down"></i> Xem thêm</span>
              </a>
            </div>

            <!-- Thông báo khi hết tour -->
            <div class="no-more" style="text-align:center; display:none; color:gray;">
              <em>Đã hiển thị tất cả tour</em>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script type="text/javascript" src="{{ asset('js/plusone.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/support_fixed.css') }}">
<script type="text/javascript" src="{{ asset('js/support_fixed.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/Drog.js') }}"></script>
@endsection