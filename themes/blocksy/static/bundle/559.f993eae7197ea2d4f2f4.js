"use strict";(self.blocksyJsonP=self.blocksyJsonP||[]).push([[559],{559:function(t,e,n){n.r(e),n.d(e,{mount:function(){return i}});const i=function(t){if(!t.nextElementSibling)return;const e=t.nextElementSibling;let n=t.getBoundingClientRect().left>innerWidth/2?"left":"right";const i=t.getBoundingClientRect(),l=e.getBoundingClientRect();let r=n;if(i.left+l.width>innerWidth&&(r="left"),i.left-l.width<0&&(r="right"),i.left+l.width>innerWidth&&i.right-l.width<0){r=n;let t=0,l=20;"left"===r&&(t=innerWidth-i.right-l),"right"===r&&(t=i.left-l),t=Math.round(t),e.style.setProperty("--theme-submenu-inline-offset",-1*t+"px")}e.dataset.placement=r}}}]);