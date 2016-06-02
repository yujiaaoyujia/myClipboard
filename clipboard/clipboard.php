<?php include_once APPPATH.'views/common/header.php';?>

<nav class="navbar navbar-inverse navbar-static-top" role="navigation">
	<span class="navbar-brand">推广工具</span>
</nav>

<article class="container pro-index pro-tools">
  <section class="promotion">
    <a class="col-xs-4" href="#" data-toggle="modal" data-target="#popup-register">
        <span class="pro-icon register" aria-hidden="true"></span>
        <h5>注册</h5>
      </a>
      <a class="col-xs-4" href="#" data-toggle="modal" data-target="#popup-public">
        <span class="pro-icon public" aria-hidden="true"></span>
        <h5>公众号</h5>
      </a>
  </section>
</article>

<div class="pro-tools alert alert-warning alert-dismissible fade in" role="alert">
  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
  <strong>注意！</strong>您还没有设置个人渠道号
</div>

<article id="popup-register" class="pro-tools modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm">
    <div class="modal-content text-center">
      <h3>推广注册</h3>
      <p>出示二维码或复制链接可进行推广注册</p>
      <div class="qrcodeTable"></div>
      <h3>
        <a id="js-copybtn" tabindex="0" role="button" class="btn btn-primary" data-toggle="popover" data-placement="bottom" data-content="复制成功！">复制链接</a>
      </h3>
    </div>
  </div>
</article>

<article id="popup-public" class="pro-tools modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm">
      <div class="modal-content text-center">
        <h3>微信公众号</h3>
        <p>关注公众号可获取更多优惠信息及活动</p>
        <img class="qrcodePublic" width="200" height="200" src="/resource/images/public.jpg" />
        <h5><small>请微信扫描二维码关注公众号</small></h5>
      </div>
  </div>
</article>

<script type="text/javascript" src="/resource/js/promotion_tools/jquery.qrcode.min.js"></script>
<script type="text/javascript" src="/resource/js/promotion_tools/clipboard.min.js"></script>
<script type="text/javascript">
  // 获取参数
  var host = '<?php echo $new_register_url; ?>';
  var ddb_src_id = '<?php echo $ddb_src_id; ?>';
  var pro_url = host + '/h5/invite_promotion/index.html?ddb_src_id=';
  if(!ddb_src_id){
    $('.pro-tools.alert').show();
  }else{
    pro_url += ddb_src_id;
  }

  // cookie操作
  function setCookie(c_name,value,expiredays) {
  	var exdate=new Date()
  	exdate.setDate(exdate.getDate()+expiredays)
  	document.cookie = c_name + "=" + escape(value) + ((expiredays==null) ? "" : ";expires=" + exdate.toGMTString())
  }
  function getCookie(c_name) {
  	if (document.cookie.length>0) {
  		c_start=document.cookie.indexOf(c_name + "=")
  		if (c_start!=-1) {
  			c_start=c_start + c_name.length+1
  			c_end=document.cookie.indexOf(";",c_start)
  			if (c_end==-1) c_end=document.cookie.length
  			return unescape(document.cookie.substring(c_start,c_end))
  		}
  	}
  	return ""
  }

  // 获取二维码链接
  function getQrcode( ddb_src_id, callback ){
    $.ajax({
      url: host + "/wechat_api/get_qrcode",
      data: {
        scene_id: ddb_src_id
      },
      cache: false,
      type: 'get',
      dataType: 'json'
    }).done(function(d) {
      if( 0 === +d.ret ){
        callback && callback(d.data);
      }
    });
  }

  // 设置二维码图片
  function setQrcode(url){
    $('.qrcodePublic').attr('src',url);
  }

  // 生成注册二维码
  $('.qrcodeTable').qrcode({
    width: 200,
    height: 200,
    render: 'table',
    text: pro_url
  });

  // 生成公众号二维码
  if(ddb_src_id){
    var qrcode = getCookie('qrcode_' + ddb_src_id);
    if( qrcode ){
      setQrcode(qrcode);
    }else{
      getQrcode( ddb_src_id, function(ret){
        setQrcode(ret.qrurl);
        setCookie( 'qrcode_' + ddb_src_id, ret.qrurl, Math.floor(ret.expire_seconds/(60*60*24)) );
      });
    }
  }

  // 注册二维码复制链接
  var copybtn = $('#js-copybtn')[0];
  try {
    var clipboard = new Clipboard(copybtn, {
      text: function() {
        return pro_url;
      }
    });

    clipboard.on('success', function(e) {
      $('#js-copybtn').popover('show');
      setTimeout(function(){
        $('#js-copybtn').popover('hide');
      },1500);
    });

    clipboard.on('error', function(e) {
      showCopyText(pro_url);
      $('#js-copybtn').attr('data-content', '浏览器不支持直接复制链接');
      $('#js-copybtn').popover('show');
      setTimeout(function(){
        $('#js-copybtn').popover('hide');
      },1500);
    });
  } catch(r) {
    showCopyText(pro_url);
    $('#js-copybtn').attr('data-content', '浏览器不支持直接复制链接');
    $('#js-copybtn').popover('show');
    setTimeout(function(){
      $('#js-copybtn').popover('hide');
    },1500);
  }

  // 不支持复制链接 显示要复制的文字
  function showCopyText(text) {
    var transfer = document.getElementById('js-copytransfer');
    if (!transfer) {
      transfer = document.createElement('input');
      transfer.id = 'js-copytransfer';
      $(transfer).css({
        'display': 'block',
        'margin': '-25px auto 5px',
        'border': '1px #eee solid',
        'font-size': '12px'
      });
      copybtn.parentNode.insertBefore(transfer, copybtn);
    }
    transfer.value = text;
    // transfer.select();
  }
</script>
<?php include_once APPPATH.'views/common/footer.php';?>