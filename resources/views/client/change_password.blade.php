@extends('layouts.app')

@section('title', 'Đổi mật khẩu')

@section('head')
<link rel="stylesheet" href="{{ asset('css/client/change-password.css') }}">
@endsection

@section('content')
<div id="vnt-content">
  @if ($errors->any())
  <div class="alert alert-danger">
    <ul style="margin:0; padding-left:20px;">
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif
  <div class="vhmemberpg">
    <div class="mbpadd">
      <div class="wrapper">
        <div class="hpmembermm">
          <div class="vtitlemb">
            <h1>
              <h1>Đổi mật khẩu mới</h1>
            </h1>
          </div>
          <div class="vcontsmb">
            <div class="tpmemberhh vborder vpaddall">
              <div class="hhwrap">
                <div class="boxAccount">
                  <form role="form" method="post" class="form validate" id="myForm" name="myForm" action="{{ route('account.passwordUpdate') }}">
                    @csrf
                    <div class="vformall">
                      <div class="xlabel">
                        <label for="old_password">Mật khẩu cũ <span>*</span></label>
                      </div>
                      <div class="xinput div_input">
                        <div class="input-pass">
                          <div class="i-change"><a href="javascript:void(0);" title="hide/show"></a></div>
                          <input type="password" id="old_pass" name="old_pass" class="form-control" required />
                        </div>
                      </div>
                    </div>
                    <div class="vformall">
                      <div class="xlabel">
                        <label for="new_password">Mật khẩu mới<span>*</span></label>
                      </div>
                      <div class="xinput div_input">
                        <div class="input-pass">
                          <div class="i-change"><a href="javascript:void(0);" title="hide/show"></a></div>
                          <input type="password" id="new_pass" name="new_pass" class="form-control" required />
                        </div>
                      </div>
                    </div>
                    <div class="vformall">
                      <div class="xlabel">
                        <label for="re_new_pass">Nhập khẩu mới lần nữa<span>*</span></label>
                      </div>
                      <div class="xinput div_input">
                        <div class="input-pass">
                          <div class="i-change"><a href="javascript:void(0);" title="hide/show"></a></div>
                          <input type="password" id="re_new_pass" name="re_new_pass" class="form-control" required />
                        </div>
                      </div>
                    </div>
                    <div class="vformall">
                      <div class="xbuton">
                        <button type="submit" class="btn-submit"><span>Cập nhật</span></button>
                      </div>
                    </div>
                  </form>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection