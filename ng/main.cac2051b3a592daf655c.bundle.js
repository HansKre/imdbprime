webpackJsonp([1],{"+h1B":function(n,l,t){"use strict";var e=t("/oeL"),u=t("aR8+"),o=t("wQAS"),r=t("lLfI"),i=t("URqq"),c=t("jrno"),a=t("q4dy"),d=t("qbdv"),s=t("fc+i"),_=t("bm2B"),f=t("BkNc"),g=t("GxSg"),p=t("4x4e"),h=t("nKUZ"),m=t("fmEJ"),v=t("stfM");t.d(l,"a",function(){return b});var b=e.b(u.a,[o.a],function(n){return e.c([e.d(512,e.e,e.f,[[8,[r.a,i.a,c.a,a.a]],[3,e.e],e.g]),e.d(5120,e.h,e.i,[[3,e.h]]),e.d(4608,d.a,d.b,[e.h]),e.d(5120,e.j,e.k,[]),e.d(5120,e.l,e.m,[]),e.d(5120,e.n,e.o,[]),e.d(4608,s.b,s.c,[d.c]),e.d(6144,e.p,null,[s.b]),e.d(4608,s.d,s.e,[]),e.d(5120,s.f,function(n,l,t,e){return[new s.g(n),new s.h(l),new s.i(t,e)]},[d.c,d.c,d.c,s.d]),e.d(4608,s.j,s.j,[s.f,e.q]),e.d(135680,s.k,s.k,[d.c]),e.d(4608,s.l,s.l,[s.j,s.k]),e.d(6144,e.r,null,[s.l]),e.d(6144,s.m,null,[s.k]),e.d(4608,e.s,e.s,[e.q]),e.d(4608,s.n,s.n,[d.c]),e.d(4608,s.o,s.o,[d.c]),e.d(4608,_.a,_.a,[]),e.d(5120,f.a,f.b,[f.c]),e.d(4608,f.d,f.d,[]),e.d(6144,f.e,null,[f.d]),e.d(135680,f.f,f.f,[f.c,e.t,e.u,e.v,f.e]),e.d(4608,f.g,f.g,[]),e.d(5120,f.h,f.i,[f.j]),e.d(5120,e.w,function(n){return[n]},[f.h]),e.d(4608,g.a,g.a,[]),e.d(512,d.d,d.d,[]),e.d(1024,e.x,s.p,[]),e.d(1024,e.y,function(){return[f.k()]},[]),e.d(512,f.j,f.j,[e.v]),e.d(1024,e.z,function(n,l,t){return[s.q(n,l),f.l(t)]},[[2,s.r],[2,e.y],f.j]),e.d(512,e.A,e.A,[[2,e.z]]),e.d(131584,e.B,e.B,[e.q,e.C,e.v,e.x,e.e,e.A]),e.d(2048,e.D,null,[e.B]),e.d(512,e.E,e.E,[e.D]),e.d(512,s.s,s.s,[[3,s.s]]),e.d(512,_.b,_.b,[]),e.d(512,_.c,_.c,[]),e.d(1024,f.m,f.n,[[3,f.c]]),e.d(512,f.o,f.p,[]),e.d(512,f.q,f.q,[]),e.d(256,f.r,{},[]),e.d(1024,d.e,f.s,[d.f,[2,d.g],f.r]),e.d(512,d.h,d.h,[d.e]),e.d(512,e.u,e.u,[]),e.d(512,e.t,e.F,[e.u,[2,e.G]]),e.d(1024,f.t,function(){return[[{path:"",redirectTo:"/dashboard",pathMatch:"full"},{path:"heroes",component:p.a},{path:"dashboard",component:h.a},{path:"detail/:id",component:m.a}]]},[]),e.d(1024,f.c,f.u,[e.D,f.o,f.q,d.h,e.v,e.t,e.u,f.t,f.r,[2,f.v],[2,f.w]]),e.d(512,f.x,f.x,[[2,f.m],[2,f.c]]),e.d(512,v.a,v.a,[]),e.d(512,u.a,u.a,[])])})},0:function(n,l,t){n.exports=t("cDNt")},"4x4e":function(n,l,t){"use strict";var e=t("GxSg"),u=t("BkNc");t.d(l,"a",function(){return o});var o=function(){function n(n,l){this.heroService=n,this.router=l,this.title="Tour of Heroes"}return n.prototype.onSelect=function(n){this.selectedHero==n?this.selectedHero=null:this.selectedHero=n},n.prototype.getHeroes=function(){var n=this;this.heroService.getHeroesSlowly().then(function(l){return n.heroes=l})},n.prototype.ngOnInit=function(){this.getHeroes()},n.prototype.gotoDetail=function(){this.router.navigate(["detail",this.selectedHero.id])},n.ctorParameters=function(){return[{type:e.a},{type:u.c}]},n}()},DxQJ:function(n,l,t){"use strict";t.d(l,"a",function(){return e});var e=[{id:11,name:"Mr. Nice"},{id:12,name:"Narco"},{id:13,name:"Bombasto"},{id:14,name:"Celeritas"},{id:15,name:"Magneta"},{id:16,name:"RubberMan"},{id:17,name:"Dynama"},{id:18,name:"Dr IQ"},{id:19,name:"Magma"},{id:20,name:"Tornado"}]},GxSg:function(n,l,t){"use strict";var e=t("DxQJ");t.d(l,"a",function(){return u});var u=function(){function n(){}return n.prototype.getHeroes=function(){return Promise.resolve(e.a)},n.prototype.getHeroesSlowly=function(){var n=this;return new Promise(function(l){setTimeout(function(){return l(n.getHeroes())},500)})},n.prototype.getHeroSlowly=function(n){return this.getHeroesSlowly().then(function(l){return l.find(function(l){return l.id===n})})},n.ctorParameters=function(){return[]},n}()},NhKt:function(n,l,t){"use strict";t.d(l,"a",function(){return e});var e=["h1[_ngcontent-%COMP%]{font-size:1.2em;color:#999;margin-bottom:0}h2[_ngcontent-%COMP%]{font-size:2em;margin-top:0;padding-top:0}nav[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]{padding:5px 10px;text-decoration:none;margin-top:10px;display:inline-block;background-color:#eee;border-radius:4px}a[_ngcontent-%COMP%]:link, nav[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]:visited{color:#607d8b}nav[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]:hover{background-color:#cfd8dc}nav[_ngcontent-%COMP%]   a.active[_ngcontent-%COMP%], nav[_ngcontent-%COMP%]   a[_ngcontent-%COMP%]:hover{color:#039be5}"]},Rd3u:function(n,l,t){"use strict";t.d(l,"a",function(){return e});var e=[".selected[_ngcontent-%COMP%]{background-color:#cfd8dc!important;color:#fff}.heroes[_ngcontent-%COMP%]{margin:0 0 2em 0;list-style-type:none;padding:0;width:15em}.heroes[_ngcontent-%COMP%]   li[_ngcontent-%COMP%]{cursor:pointer;position:relative;left:0;background-color:#eee;margin:.5em;padding:.3em 0;height:1.6em;border-radius:4px}.heroes[_ngcontent-%COMP%]   li[_ngcontent-%COMP%]:hover{color:#607d8b;background-color:#ddd;left:.1em}.heroes[_ngcontent-%COMP%]   li.selected[_ngcontent-%COMP%]:hover{background-color:#bbd8dc!important;color:#fff}.heroes[_ngcontent-%COMP%]   .text[_ngcontent-%COMP%]{position:relative;top:-3px}.heroes[_ngcontent-%COMP%]   .badge[_ngcontent-%COMP%]{display:inline-block;font-size:small;color:#fff;padding:.8em .7em 0 .7em;background-color:#607d8b;line-height:1em;position:relative;left:-1px;top:-4px;height:1.8em;margin-right:.8em;border-radius:4px 0 0 4px}button[_ngcontent-%COMP%]{font-family:Arial;background-color:#eee;border:none;padding:5px 10px;border-radius:4px;cursor:pointer;cursor:hand}button[_ngcontent-%COMP%]:hover{background-color:#cfd8dc}"]},TCNN:function(n,l,t){"use strict";t.d(l,"a",function(){return e});var e=["label[_ngcontent-%COMP%]{display:inline-block;width:3em;margin:.5em 0;color:#607d8b;font-weight:700}input[_ngcontent-%COMP%]{height:2em;font-size:1em;padding-left:.4em}button[_ngcontent-%COMP%]{margin-top:20px;font-family:Arial;background-color:#eee;border:none;padding:5px 10px;border-radius:4px;cursor:pointer;cursor:hand}button[_ngcontent-%COMP%]:hover{background-color:#cfd8dc}button[_ngcontent-%COMP%]:disabled{background-color:#eee;color:#ccc;cursor:auto}"]},URqq:function(n,l,t){"use strict";function e(n){return i._26(0,[(n()(),i._27(0,null,null,9,"a",[["class","col-1-4"]],[[1,"target",0],[8,"href",4]],[[null,"click"]],function(n,l,t){var e=!0;if("click"===l){e=!1!==i._29(n,1).onClick(t.button,t.ctrlKey,t.metaKey,t.shiftKey)&&e}return e},null,null)),i._30(671744,null,0,c.y,[c.c,c.a,a.e],{routerLink:[0,"routerLink"]},null),i._35(2),(n()(),i._28(null,["\n    "])),(n()(),i._27(0,null,null,4,"div",[["class","module hero"]],null,null,null,null,null)),(n()(),i._28(null,["\n      "])),(n()(),i._27(0,null,null,1,"h4",[],null,null,null,null,null)),(n()(),i._28(null,["",""])),(n()(),i._28(null,["\n    "])),(n()(),i._28(null,["\n  "]))],function(n,l){n(l,1,0,n(l,2,0,"/detail",l.context.$implicit.id))},function(n,l){n(l,0,0,i._29(l,1).target,i._29(l,1).href),n(l,7,0,l.context.$implicit.name)})}function u(n){return i._26(0,[(n()(),i._27(0,null,null,1,"h3",[],null,null,null,null,null)),(n()(),i._28(null,["Top Heroes"])),(n()(),i._28(null,["\n"])),(n()(),i._27(0,null,null,4,"div",[["class","grid grid-pad"]],null,null,null,null,null)),(n()(),i._28(null,["\n  "])),(n()(),i._34(16777216,null,null,1,null,e)),i._30(802816,null,0,a.o,[i.W,i._8,i.l],{ngForOf:[0,"ngForOf"]},null),(n()(),i._28(null,["\n"])),(n()(),i._28(null,["\n"]))],function(n,l){n(l,6,0,l.component.heroes)},null)}function o(n){return i._26(0,[(n()(),i._27(0,null,null,1,"my-dashboard",[],null,null,null,u,f)),i._30(114688,null,0,d.a,[s.a],null,null)],function(n,l){n(l,1,0)},null)}var r=t("pc53"),i=t("/oeL"),c=t("BkNc"),a=t("qbdv"),d=t("nKUZ"),s=t("GxSg");t.d(l,"a",function(){return g});var _=[r.a],f=i._25({encapsulation:0,styles:_,data:{}}),g=i._32("my-dashboard",d.a,o,{},{},[])},"aR8+":function(n,l,t){"use strict";t.d(l,"a",function(){return e});var e=function(){function n(){}return n}()},cDNt:function(n,l,t){"use strict";Object.defineProperty(l,"__esModule",{value:!0});var e=t("/oeL"),u=t("p5Ee"),o=t("+h1B"),r=t("fc+i");u.a.production&&t.i(e.a)(),t.i(r.a)().bootstrapModuleFactory(o.a)},fmEJ:function(n,l,t){"use strict";var e=t("GxSg"),u=t("BkNc"),o=t("qbdv"),r=t("Pic8");t.n(r);t.d(l,"a",function(){return i});var i=function(){function n(n,l,t){this.heroService=n,this.route=l,this.location=t}return n.prototype.ngOnInit=function(){var n=this;this.route.paramMap.switchMap(function(l){return n.heroService.getHeroSlowly(+l.get("id"))}).subscribe(function(l){return n.hero=l})},n.prototype.goBack=function(){this.location.back()},n.ctorParameters=function(){return[{type:e.a},{type:u.a},{type:o.h}]},n}()},jrno:function(n,l,t){"use strict";function e(n){return i._26(0,[(n()(),i._27(0,null,null,26,"div",[],null,null,null,null,null)),(n()(),i._28(null,["\n  "])),(n()(),i._27(0,null,null,1,"h2",[],null,null,null,null,null)),(n()(),i._28(null,[""," details!"])),(n()(),i._28(null,["\n  "])),(n()(),i._27(0,null,null,4,"div",[],null,null,null,null,null)),(n()(),i._28(null,["\n    "])),(n()(),i._27(0,null,null,1,"label",[],null,null,null,null,null)),(n()(),i._28(null,["id: "])),(n()(),i._28(null,["",""])),(n()(),i._28(null,["\n  "])),(n()(),i._27(0,null,null,11,"div",[],null,null,null,null,null)),(n()(),i._28(null,["\n    "])),(n()(),i._27(0,null,null,1,"label",[],null,null,null,null,null)),(n()(),i._28(null,["name: "])),(n()(),i._28(null,["\n    "])),(n()(),i._27(0,null,null,5,"input",[["placeholder","name"]],[[2,"ng-untouched",null],[2,"ng-touched",null],[2,"ng-pristine",null],[2,"ng-dirty",null],[2,"ng-valid",null],[2,"ng-invalid",null],[2,"ng-pending",null]],[[null,"ngModelChange"],[null,"input"],[null,"blur"],[null,"compositionstart"],[null,"compositionend"]],function(n,l,t){var e=!0,u=n.component;if("input"===l){e=!1!==i._29(n,17)._handleInput(t.target.value)&&e}if("blur"===l){e=!1!==i._29(n,17).onTouched()&&e}if("compositionstart"===l){e=!1!==i._29(n,17)._compositionStart()&&e}if("compositionend"===l){e=!1!==i._29(n,17)._compositionEnd(t.target.value)&&e}if("ngModelChange"===l){e=!1!==(u.hero.name=t)&&e}return e},null,null)),i._30(16384,null,0,c.d,[i.O,i.P,[2,c.e]],null,null),i._33(1024,null,c.f,function(n){return[n]},[c.d]),i._30(671744,null,0,c.g,[[8,null],[8,null],[8,null],[2,c.f]],{model:[0,"model"]},{update:"ngModelChange"}),i._33(2048,null,c.h,null,[c.g]),i._30(16384,null,0,c.i,[c.h],null,null),(n()(),i._28(null,["\n  "])),(n()(),i._28(null,["\n  "])),(n()(),i._27(0,null,null,1,"button",[],null,[[null,"click"]],function(n,l,t){var e=!0,u=n.component;if("click"===l){e=!1!==u.goBack()&&e}return e},null,null)),(n()(),i._28(null,["Back"])),(n()(),i._28(null,["\n"]))],function(n,l){n(l,19,0,l.component.hero.name)},function(n,l){var t=l.component;n(l,3,0,t.hero.name),n(l,9,0,t.hero.id),n(l,16,0,i._29(l,21).ngClassUntouched,i._29(l,21).ngClassTouched,i._29(l,21).ngClassPristine,i._29(l,21).ngClassDirty,i._29(l,21).ngClassValid,i._29(l,21).ngClassInvalid,i._29(l,21).ngClassPending)})}function u(n){return i._26(0,[(n()(),i._34(16777216,null,null,1,null,e)),i._30(16384,null,0,a.n,[i.W,i._8],{ngIf:[0,"ngIf"]},null),(n()(),i._28(null,["\n"]))],function(n,l){n(l,1,0,l.component.hero)},null)}function o(n){return i._26(0,[(n()(),i._27(0,null,null,1,"hero-detail",[],null,null,null,u,g)),i._30(114688,null,0,d.a,[s.a,_.a,a.h],null,null)],function(n,l){n(l,1,0)},null)}var r=t("TCNN"),i=t("/oeL"),c=t("bm2B"),a=t("qbdv"),d=t("fmEJ"),s=t("GxSg"),_=t("BkNc");t.d(l,"a",function(){return p});var f=[r.a],g=i._25({encapsulation:0,styles:f,data:{}}),p=i._32("hero-detail",d.a,o,{hero:"hero"},{},[])},lLfI:function(n,l,t){"use strict";function e(n){return c._26(0,[(n()(),c._27(0,null,null,4,"li",[],[[2,"selected",null]],[[null,"click"]],function(n,l,t){var e=!0,u=n.component;if("click"===l){e=!1!==u.onSelect(n.context.$implicit)&&e}return e},null,null)),(n()(),c._28(null,["\n    "])),(n()(),c._27(0,null,null,1,"span",[["class","badge"]],null,null,null,null,null)),(n()(),c._28(null,["",""])),(n()(),c._28(null,[" ","\n  "]))],null,function(n,l){var t=l.component;n(l,0,0,l.context.$implicit===t.selectedHero),n(l,3,0,l.context.$implicit.id),n(l,4,0,l.context.$implicit.name)})}function u(n){return c._26(0,[(n()(),c._27(0,null,null,8,"div",[],null,null,null,null,null)),(n()(),c._28(null,["\n  "])),(n()(),c._27(0,null,null,2,"h2",[],null,null,null,null,null)),(n()(),c._28(null,["\n    "," is my hero\n  "])),c._36(1),(n()(),c._28(null,["\n  "])),(n()(),c._27(0,null,null,1,"button",[],null,[[null,"click"]],function(n,l,t){var e=!0,u=n.component;if("click"===l){e=!1!==u.gotoDetail()&&e}return e},null,null)),(n()(),c._28(null,["View Details"])),(n()(),c._28(null,["\n"]))],null,function(n,l){var t=l.component;n(l,3,0,c._37(l,3,0,n(l,4,0,c._29(l.parent,0),t.selectedHero.name)))})}function o(n){return c._26(0,[c._38(0,a.p,[]),(n()(),c._27(0,null,null,1,"h2",[],null,null,null,null,null)),(n()(),c._28(null,["My Heroes"])),(n()(),c._28(null,["\n"])),(n()(),c._27(0,null,null,4,"ul",[["class","heroes"]],null,null,null,null,null)),(n()(),c._28(null,["\n  "])),(n()(),c._34(16777216,null,null,1,null,e)),c._30(802816,null,0,a.o,[c.W,c._8,c.l],{ngForOf:[0,"ngForOf"]},null),(n()(),c._28(null,["\n"])),(n()(),c._28(null,["\n"])),(n()(),c._34(16777216,null,null,1,null,u)),c._30(16384,null,0,a.n,[c.W,c._8],{ngIf:[0,"ngIf"]},null),(n()(),c._28(null,["\n"]))],function(n,l){var t=l.component;n(l,7,0,t.heroes),n(l,11,0,t.selectedHero)},null)}function r(n){return c._26(0,[(n()(),c._27(0,null,null,1,"my-heroes",[],null,null,null,o,g)),c._30(114688,null,0,d.a,[s.a,_.c],null,null)],function(n,l){n(l,1,0)},null)}var i=t("Rd3u"),c=t("/oeL"),a=t("qbdv"),d=t("4x4e"),s=t("GxSg"),_=t("BkNc");t.d(l,"a",function(){return p});var f=[i.a],g=c._25({encapsulation:0,styles:f,data:{}}),p=c._32("my-heroes",d.a,r,{},{},[])},nKUZ:function(n,l,t){"use strict";var e=t("GxSg");t.d(l,"a",function(){return u});var u=function(){function n(n){this.heroService=n,this.heroes=[]}return n.prototype.ngOnInit=function(){var n=this;this.heroService.getHeroes().then(function(l){return n.heroes=l.slice(1,5)})},n.ctorParameters=function(){return[{type:e.a}]},n}()},p5Ee:function(n,l,t){"use strict";t.d(l,"a",function(){return e});var e={production:!0}},pc53:function(n,l,t){"use strict";t.d(l,"a",function(){return e});var e=["[class*=col-][_ngcontent-%COMP%]{float:left;padding-right:20px;padding-bottom:20px}[class*=col-][_ngcontent-%COMP%]:last-of-type{padding-right:0}a[_ngcontent-%COMP%]{text-decoration:none}*[_ngcontent-%COMP%], [_ngcontent-%COMP%]:after, [_ngcontent-%COMP%]:before{box-sizing:border-box}h3[_ngcontent-%COMP%]{text-align:center;margin-bottom:0}h4[_ngcontent-%COMP%]{position:relative}.grid[_ngcontent-%COMP%]{margin:0}.col-1-4[_ngcontent-%COMP%]{width:25%}.module[_ngcontent-%COMP%]{padding:20px;text-align:center;color:#eee;max-height:120px;min-width:120px;background-color:#607d8b;border-radius:2px}.module[_ngcontent-%COMP%]:hover{background-color:#eee;cursor:pointer;color:#607d8b}.grid-pad[_ngcontent-%COMP%]{padding:10px 0}.grid-pad[_ngcontent-%COMP%] > [class*=col-][_ngcontent-%COMP%]:last-of-type{padding-right:20px}@media (max-width:600px){.module[_ngcontent-%COMP%]{font-size:10px;max-height:75px}}@media (max-width:1024px){.grid[_ngcontent-%COMP%]{margin:0}.module[_ngcontent-%COMP%]{min-width:60px}}"]},q4dy:function(n,l,t){"use strict";function e(n){return r._26(0,[(n()(),r._27(0,null,null,1,"h1",[],null,null,null,null,null)),(n()(),r._28(null,["",""])),(n()(),r._28(null,["\n"])),(n()(),r._27(0,null,null,15,"nav",[],null,null,null,null,null)),(n()(),r._28(null,["\n    "])),(n()(),r._27(0,null,null,5,"a",[["routerLink","/dashboard"],["routerLinkActive","active"]],[[1,"target",0],[8,"href",4]],[[null,"click"]],function(n,l,t){var e=!0;if("click"===l){e=!1!==r._29(n,6).onClick(t.button,t.ctrlKey,t.metaKey,t.shiftKey)&&e}return e},null,null)),r._30(671744,[[2,4]],0,i.y,[i.c,i.a,c.e],{routerLink:[0,"routerLink"]},null),r._30(1720320,null,2,i.z,[i.c,r.P,r.O,r.T],{routerLinkActive:[0,"routerLinkActive"]},null),r._31(603979776,1,{links:1}),r._31(603979776,2,{linksWithHrefs:1}),(n()(),r._28(null,["Dashboard"])),(n()(),r._28(null,["\n    "])),(n()(),r._27(0,null,null,5,"a",[["routerLink","/heroes"],["routerLinkActive","active"]],[[1,"target",0],[8,"href",4]],[[null,"click"]],function(n,l,t){var e=!0;if("click"===l){e=!1!==r._29(n,13).onClick(t.button,t.ctrlKey,t.metaKey,t.shiftKey)&&e}return e},null,null)),r._30(671744,[[4,4]],0,i.y,[i.c,i.a,c.e],{routerLink:[0,"routerLink"]},null),r._30(1720320,null,2,i.z,[i.c,r.P,r.O,r.T],{routerLinkActive:[0,"routerLinkActive"]},null),r._31(603979776,3,{links:1}),r._31(603979776,4,{linksWithHrefs:1}),(n()(),r._28(null,["Heroes"])),(n()(),r._28(null,["\n"])),(n()(),r._28(null,["\n"])),(n()(),r._27(16777216,null,null,1,"router-outlet",[],null,null,null,null,null)),r._30(212992,null,0,i.A,[i.q,r.W,r.e,[8,null],r.T],null,null)],function(n,l){n(l,6,0,"/dashboard");n(l,7,0,"active");n(l,13,0,"/heroes");n(l,14,0,"active"),n(l,21,0)},function(n,l){n(l,1,0,l.component.title),n(l,5,0,r._29(l,6).target,r._29(l,6).href),n(l,12,0,r._29(l,13).target,r._29(l,13).href)})}function u(n){return r._26(0,[(n()(),r._27(0,null,null,1,"my-navigator-component",[],null,null,null,e,s)),r._30(49152,null,0,a.a,[],null,null)],null,null)}var o=t("NhKt"),r=t("/oeL"),i=t("BkNc"),c=t("qbdv"),a=t("wQAS");t.d(l,"a",function(){return _});var d=[o.a],s=r._25({encapsulation:0,styles:d,data:{}}),_=r._32("my-navigator-component",a.a,u,{},{},[])},qtrl:function(n,l){function t(n){throw new Error("Cannot find module '"+n+"'.")}t.keys=function(){return[]},t.resolve=t,n.exports=t,t.id="qtrl"},stfM:function(n,l,t){"use strict";var e=t("fmEJ"),u=t("nKUZ"),o=t("4x4e");t.d(l,"a",function(){return r});var r=(o.a,u.a,e.a,function(){function n(){}return n}())},wQAS:function(n,l,t){"use strict";t.d(l,"a",function(){return e});var e=function(){function n(){this.title="Tour of Heroes"}return n}()}},[0]);