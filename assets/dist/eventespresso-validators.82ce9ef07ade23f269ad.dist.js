!function(){"use strict";var e={d:function(t,n){for(var r in n)e.o(n,r)&&!e.o(t,r)&&Object.defineProperty(t,r,{enumerable:!0,get:n[r]})},o:function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},r:function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})}},t={};e.r(t),e.d(t,{instanceOf:function(){return m},isGenerator:function(){return n},isModelEntity:function(){return s},isModelEntityFactory:function(){return o},isModelEntityFactoryOfModel:function(){return i},isModelEntityOfModel:function(){return c},isSchema:function(){return u},isSchemaOfModel:function(){return l},isSchemaResponse:function(){return a},isSchemaResponseOfModel:function(){return f}});const n=e=>!!e&&"Generator"===e[Symbol.toStringTag];var r=window.lodash;const o=e=>!!e&&!(0,r.isUndefined)(e.classDef)&&!(0,r.isUndefined)(e.modelName)&&"BaseEntity"===Object.getPrototypeOf(e.classDef).name,i=(e,t)=>o(e)&&e.modelName===t,s=e=>(0,r.isObject)(e)&&"BaseEntity"===Object.getPrototypeOf(e.constructor).name,c=(e,t)=>(t=(0,r.upperFirst)((0,r.camelCase)(t)),s(e)&&e.constructor.name===t),a=e=>d(e)&&u(e.schema),u=e=>(0,r.isPlainObject)(e)&&!(0,r.isUndefined)(e.$schema)&&(0,r.isPlainObject)(e.properties),f=(e,t)=>d(e)&&l(e.schema,t),l=(e,t)=>u(e)&&!(0,r.isUndefined)(e.title)&&(0,r.lowerCase)(t)===(0,r.lowerCase)(e.title),d=e=>(0,r.isPlainObject)(e)&&!(0,r.isUndefined)(e.schema);function m(e,t){if(!e)return!1;if(e.constructor){if(e.constructor.name&&e.constructor.name===t)return!0;if(e.constructor.displayName&&e.constructor.displayName===t)return!0}return e.hasOwnProperty("displayName")&&e.displayName===t}(this.eejs=this.eejs||{}).validators=t}();