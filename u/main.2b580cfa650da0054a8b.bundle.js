webpackJsonp([1],{"+h1B":function(n,l,u){"use strict";var t=u("/oeL"),o=u("aR8+"),e=u("wQAS"),r=u("q4dy"),i=u("qbdv"),a=u("fc+i"),c=u("bm2B"),s=u("CPp0"),d=u("E/Rl");u.d(l,"a",function(){return p});var p=t.b(o.a,[e.a],function(n){return t.c([t.d(512,t.e,t.f,[[8,[r.a]],[3,t.e],t.g]),t.d(5120,t.h,t.i,[[3,t.h]]),t.d(4608,i.a,i.b,[t.h]),t.d(4608,t.j,t.j,[]),t.d(5120,t.k,t.l,[]),t.d(5120,t.m,t.n,[]),t.d(5120,t.o,t.p,[]),t.d(4608,a.b,a.c,[i.c]),t.d(6144,t.q,null,[a.b]),t.d(4608,a.d,a.e,[]),t.d(5120,a.f,function(n,l,u,t){return[new a.g(n),new a.h(l),new a.i(u,t)]},[i.c,i.c,i.c,a.d]),t.d(4608,a.j,a.j,[a.f,t.r]),t.d(135680,a.k,a.k,[i.c]),t.d(4608,a.l,a.l,[a.j,a.k]),t.d(6144,t.s,null,[a.l]),t.d(6144,a.m,null,[a.k]),t.d(4608,t.t,t.t,[t.r]),t.d(4608,a.n,a.n,[i.c]),t.d(4608,a.o,a.o,[i.c]),t.d(4608,c.a,c.a,[]),t.d(4608,s.a,s.a,[]),t.d(4608,s.b,s.c,[]),t.d(5120,s.d,s.e,[]),t.d(4608,s.f,s.f,[s.a,s.b,s.d]),t.d(4608,s.g,s.h,[]),t.d(5120,s.i,s.j,[s.f,s.g]),t.d(4608,d.a,d.a,[s.i]),t.d(512,i.d,i.d,[]),t.d(1024,t.u,a.p,[]),t.d(1024,t.v,function(n,l){return[a.q(n,l)]},[[2,a.r],[2,t.w]]),t.d(512,t.x,t.x,[[2,t.v]]),t.d(131584,t.y,t.y,[t.r,t.z,t.A,t.u,t.e,t.x]),t.d(2048,t.B,null,[t.y]),t.d(512,t.C,t.C,[t.B]),t.d(512,a.s,a.s,[[3,a.s]]),t.d(512,c.b,c.b,[]),t.d(512,c.c,c.c,[]),t.d(512,s.k,s.k,[]),t.d(512,o.a,o.a,[])])})},1:function(n,l,u){n.exports=u("cDNt")},"E/Rl":function(n,l,u){"use strict";var t=u("CPp0");u.d(l,"a",function(){return o});var o=function(){function n(n){this.http=n}return n.prototype.getObservable=function(){return this.http.get("http://imdbprime-snah.rhcloud.com/getMoviesWithRatings.php?sortBy=ratingValue&order=descending&ratingCountMin=10000")},n.prototype.getPromise=function(){return this.promise?(console.log("Retruning cached promise"),this.promise):(this.promise=new Promise(function(n,l){var u=this;this.http.get("http://imdbprime-snah.rhcloud.com/getMoviesWithRatings.php?sortBy=ratingValue&order=descending&ratingCountMin=10000").subscribe(function(n){return u.handleResponse(n)},function(n){return l(n)},function(){return n(u.movies)})}.bind(this)),this.promise)},n.prototype.handleResponse=function(n){var l,u=!0;l=n.json(),l instanceof Array?l.forEach(function(n){void 0!==n.movie&&void 0!==n.year&&void 0!==n.imdbMovieUrl&&void 0!==n.director&&void 0!==n.ratingValue&&void 0!==n.ratingCount||(console.log("Unknown type: "+n.valueOf()),u=!1)}):(u=!1,console.log("Not an array")),u&&(this.movies=l)},n.ctorParameters=function(){return[{type:t.i}]},n}()},NhKt:function(n,l,u){"use strict";u.d(l,"a",function(){return t});var t=[""]},XhjD:function(n,l,u){"use strict";var t=u("E/Rl");u.d(l,"a",function(){return o});var o=function(){function n(n){var l=this;this.webService=n,this.allowNewServer=!1,this.updatedMovies=!1,setTimeout(function(){l.allowNewServer=!0},2e3)}return n.prototype.ngOnInit=function(){this.preLoadMoviesFromLocalStorage(),this.registerForWebRequest()},n.prototype.preLoadMoviesFromLocalStorage=function(){localStorage&&localStorage.movies&&(this.movies=JSON.parse(localStorage.movies))},n.prototype.storeMoviesToLocalStorage=function(){localStorage&&(localStorage.movies=JSON.stringify(this.movies))},n.prototype.registerForWebRequest=function(){this.webService.getPromise().then(function(n){this.movies=n,this.storeMoviesToLocalStorage(),this.updatedMovies=!0}.bind(this),function(n){alert("Movies could not be retrieved from the web service."),console.log(n)}.bind(this))},n.prototype.useLocalStorage=function(){localStorage.pageLoadCount||(localStorage.pageLoadCount=0),localStorage.pageLoadCount=parseInt(localStorage.pageLoadCount)+1,alert(localStorage.pageLoadCount)},n.prototype.wasOnline=function(){return this.updatedMovies},n.ctorParameters=function(){return[{type:t.a}]},n}()},"aR8+":function(n,l,u){"use strict";u.d(l,"a",function(){return t});var t=function(){function n(){}return n}()},cDNt:function(n,l,u){"use strict";Object.defineProperty(l,"__esModule",{value:!0});var t=u("/oeL"),o=u("p5Ee"),e=u("+h1B"),r=u("fc+i");o.a.production&&u.i(t.a)(),u.i(r.a)().bootstrapModuleFactory(e.a)},"gcb/":function(n,l,u){"use strict";u.d(l,"a",function(){return t});var t=["p[_ngcontent-%COMP%]{padding:20px;background-color:#98fb98;border:1px solid green}"]},p5Ee:function(n,l,u){"use strict";u.d(l,"a",function(){return t});var t={production:!0}},q4dy:function(n,l,u){"use strict";function t(n){return r._17(0,[(n()(),r._18(0,null,null,8,"div",[["class","container-fluid"]],null,null,null,null,null)),(n()(),r._19(null,["\n"])),(n()(),r._18(0,null,null,1,"h1",[],null,null,null,null,null)),(n()(),r._19(null,["Prime Movies with IMDB Rating"])),(n()(),r._19(null,["\n    "])),(n()(),r._18(0,null,null,2,"app-server",[],null,null,null,i.a,i.b)),r._20(114688,null,0,a.a,[c.a],null,null),(n()(),r._19(null,["Loading ..."])),(n()(),r._19(null,["\n"])),(n()(),r._19(null,["\n"]))],function(n,l){n(l,6,0)},null)}function o(n){return r._17(0,[(n()(),r._18(0,null,null,1,"app-root",[],null,null,null,t,p)),r._20(49152,null,0,s.a,[],null,null)],null,null)}var e=u("NhKt"),r=u("/oeL"),i=u("wDqv"),a=u("XhjD"),c=u("E/Rl"),s=u("wQAS");u.d(l,"a",function(){return f});var d=[e.a],p=r._16({encapsulation:0,styles:d,data:{}}),f=r._21("app-root",s.a,o,{},{},[])},qtrl:function(n,l){function u(n){throw new Error("Cannot find module '"+n+"'.")}u.keys=function(){return[]},u.resolve=u,n.exports=u,u.id="qtrl"},wDqv:function(n,l,u){"use strict";function t(n){return a._17(0,[(n()(),a._18(0,null,null,1,"p",[],null,null,null,null,null)),(n()(),a._19(null,[" Running in offline mode! "]))],null,null)}function o(n){return a._17(0,[(n()(),a._19(null,["\n        "])),(n()(),a._18(0,null,null,16,"tr",[],null,null,null,null,null)),(n()(),a._19(null,["\n            "])),(n()(),a._18(0,null,null,4,"td",[],null,null,null,null,null)),(n()(),a._19(null,["\n                "])),(n()(),a._18(0,null,null,1,"a",[["target","_blank"]],[[8,"href",4]],null,null,null,null)),(n()(),a._19(null,[" "," "])),(n()(),a._19(null,["\n            "])),(n()(),a._19(null,["\n            "])),(n()(),a._18(0,null,null,1,"td",[],null,null,null,null,null)),(n()(),a._19(null,["",""])),(n()(),a._19(null,["\n            "])),(n()(),a._18(0,null,null,1,"td",[],null,null,null,null,null)),(n()(),a._19(null,["",""])),(n()(),a._19(null,["\n            "])),(n()(),a._18(0,null,null,1,"td",[],null,null,null,null,null)),(n()(),a._19(null,["",""])),(n()(),a._19(null,["\n        "])),(n()(),a._19(null,["\n    "]))],null,function(n,l){n(l,5,0,a._22(1,"",l.context.$implicit.imdbMovieUrl,"")),n(l,6,0,l.context.$implicit.movie),n(l,10,0,l.context.$implicit.year),n(l,13,0,l.context.$implicit.ratingValue),n(l,16,0,l.context.$implicit.ratingCount)})}function e(n){return a._17(0,[(n()(),a._19(null,["\n"])),(n()(),a._19(null,["\n\n"])),(n()(),a._23(16777216,null,null,1,null,t)),a._20(16384,null,0,c.h,[a._2,a._3],{ngIf:[0,"ngIf"]},null),(n()(),a._19(null,["\n\n"])),(n()(),a._18(0,null,null,27,"table",[["class","table table-striped table-responsive"]],null,null,null,null,null)),(n()(),a._19(null,["\n    "])),(n()(),a._18(0,null,null,16,"thead",[["class","thead-inverse"]],null,null,null,null,null)),(n()(),a._19(null,["\n        "])),(n()(),a._18(0,null,null,13,"tr",[],null,null,null,null,null)),(n()(),a._19(null,["\n            "])),(n()(),a._18(0,null,null,1,"th",[["class","col-xs-8"]],null,null,null,null,null)),(n()(),a._19(null,["Movie"])),(n()(),a._19(null,["\n            "])),(n()(),a._18(0,null,null,1,"th",[["class","col-xs-1"]],null,null,null,null,null)),(n()(),a._19(null,["Year"])),(n()(),a._19(null,["\n            "])),(n()(),a._18(0,null,null,1,"th",[["class","col-xs-1"]],null,null,null,null,null)),(n()(),a._19(null,["IMDB Rating"])),(n()(),a._19(null,["\n            "])),(n()(),a._18(0,null,null,1,"th",[["class","col-xs-2"]],null,null,null,null,null)),(n()(),a._19(null,["Rating Count"])),(n()(),a._19(null,["\n        "])),(n()(),a._19(null,["\n    "])),(n()(),a._19(null,["\n\n    "])),(n()(),a._19(null,["\n    "])),(n()(),a._18(0,null,null,5,"tbody",[],null,null,null,null,null)),(n()(),a._19(null,["\n    "])),(n()(),a._23(16777216,null,null,1,null,o)),a._20(802816,null,0,c.i,[a._2,a._3,a.m],{ngForOf:[0,"ngForOf"]},null),(n()(),a._19(null,["\n\n    "])),(n()(),a._19(null,["\n    "])),(n()(),a._19(null,["\n"])),(n()(),a._19(null,["\n"]))],function(n,l){var u=l.component;n(l,3,0,!u.wasOnline()),n(l,29,0,u.movies)},null)}function r(n){return a._17(0,[(n()(),a._18(0,null,null,1,"app-server",[],null,null,null,e,f)),a._20(114688,null,0,s.a,[d.a],null,null)],function(n,l){n(l,1,0)},null)}var i=u("gcb/"),a=u("/oeL"),c=u("qbdv"),s=u("XhjD"),d=u("E/Rl");u.d(l,"b",function(){return f}),l.a=e;var p=[i.a],f=a._16({encapsulation:0,styles:p,data:{}});a._21("app-server",s.a,r,{},{},[])},wQAS:function(n,l,u){"use strict";u.d(l,"a",function(){return t});var t=function(){function n(){}return n}()}},[1]);