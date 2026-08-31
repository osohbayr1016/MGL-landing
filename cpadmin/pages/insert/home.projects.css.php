<style>
.hp-grid{display:flex;flex-wrap:wrap;margin:0 -10px;}
.hp-slot{position:relative;flex:0 0 33.33%;max-width:33.33%;padding:0 10px 20px;box-sizing:border-box;cursor:pointer;}
.hp-slot img.hp-img{display:block;width:100%;height:180px;object-fit:cover;background:#1a1a1a;}
.hp-slot .hp-copy{position:absolute;left:10px;right:10px;bottom:20px;padding:16px;background:linear-gradient(180deg,transparent,rgba(0,0,0,.7));color:#fff;}
.hp-slot .hp-name{color:#fff;margin:0 0 4px;font-size:14px;}
.hp-slot .hp-brand{color:#ddd;font-size:12px;}
.hp-slot .hp-index{position:absolute;top:8px;left:18px;z-index:2;background:rgba(0,0,0,.55);color:#fff;font-size:11px;padding:2px 6px;}
.hp-slot.hp-empty img.hp-img{background:#e8e8e8;}
.hp-slot.hp-empty .hp-copy{color:#666;background:transparent;}
.hp-slot.hp-empty .hp-name{color:#666;}
.hp-placeholder{flex:0 0 33.33%;max-width:33.33%;padding:0 10px 20px;min-height:180px;background:#f3fcf9;border:1px dashed #1ab394;box-sizing:border-box;}
.hp-slot.ui-sortable-helper{opacity:.85;}
.hp-picker-grid{display:flex;flex-wrap:wrap;margin:0 -6px;max-height:420px;overflow:auto;}
.hp-pick{flex:0 0 25%;max-width:25%;padding:6px;border:0;background:transparent;text-align:left;cursor:pointer;}
.hp-pick img{display:block;width:100%;height:90px;object-fit:cover;background:#ddd;}
.hp-pick span{display:block;font-size:11px;margin-top:4px;line-height:1.3;}
.hp-pick.is-current img{outline:3px solid #1ab394;}
@media (max-width:992px){
    .hp-slot,.hp-placeholder{flex-basis:50%;max-width:50%;}
    .hp-pick{flex-basis:33.33%;max-width:33.33%;}
}
@media (max-width:768px){
    .hp-slot,.hp-placeholder{flex-basis:100%;max-width:100%;}
    .hp-pick{flex-basis:50%;max-width:50%;}
}
</style>
