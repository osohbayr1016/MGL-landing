<style>
.cl-bar{display:flex;flex-wrap:wrap;align-items:flex-end;gap:16px;margin-bottom:20px;}
.cl-bar .form-group{margin-bottom:0;}
.cl-row{display:flex;align-items:flex-start;gap:14px;padding:14px;margin-bottom:10px;background:#fff;border:1px solid #e7eaec;}
.cl-row.cl-removed{opacity:.4;}
.cl-handle{flex:0 0 26px;padding-top:28px;color:#aaa;cursor:move;text-align:center;font-size:16px;}
.cl-logo{flex:0 0 170px;}
.cl-logo-box{display:flex;align-items:center;justify-content:center;height:88px;background:#f5f5f5;border:1px solid #e7eaec;overflow:hidden;}
.cl-logo-box img{max-width:90%;max-height:78px;width:auto;height:auto;filter:grayscale(100%);opacity:.6;transition:filter .3s ease,opacity .3s ease;}
.cl-row:hover .cl-logo-box img{filter:grayscale(0);opacity:1;}
.cl-logo-box.cl-none:after{content:"Лого алга";color:#bbb;font-size:12px;}
.cl-logo-btns{display:flex;gap:6px;margin-top:6px;}
.cl-logo-btns .btn{flex:1 1 auto;padding:3px 6px;font-size:11px;}
.cl-fields{flex:1 1 auto;min-width:220px;}
.cl-fields .form-group{margin-bottom:8px;}
.cl-fields label{font-weight:400;font-size:12px;color:#888;margin-bottom:2px;}
.cl-side{flex:0 0 120px;padding-top:22px;text-align:right;}
.cl-side .btn{margin-bottom:6px;}
.cl-placeholder{height:118px;margin-bottom:10px;background:#f3fcf9;border:1px dashed #1ab394;}
.cl-row.ui-sortable-helper{box-shadow:0 6px 18px rgba(0,0,0,.15);}
.cl-empty{padding:30px;text-align:center;color:#999;background:#fff;border:1px dashed #ddd;}
.cl-busy{opacity:.5;pointer-events:none;}
@media (max-width:768px){
	.cl-row{flex-wrap:wrap;}
	.cl-logo{flex-basis:120px;}
	.cl-side{flex-basis:100%;padding-top:0;text-align:left;}
}
</style>
