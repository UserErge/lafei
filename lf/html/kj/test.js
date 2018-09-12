console.log("Hello World 1");
var http = require("http");

bet="0123456789";
kj="4,1,6,5,3";
ws=15;
result = function(bet, kj, w){
		
	kj=kj.split(',');
	[16, 8, 4, 2, 1].forEach(function(v, i){
		if((w&v)==0) delete kj[i];
	});
	kj=kj.filter(function(v){
		return v!=undefined;
	}).join(',');
	console.log(kj);
	kj=permutation(kj.split(','),4).map(function(v){return v.join('');});
	bet=combine(bet.split(""),4).map(function(v){return v.join("");});
	i=0;
	bet.filter(function(v){console.log(i+"--"+v);i++;});
	return bet.filter(function(v){return kj.indexOf(v)!=-1}).length;
}

/**
 * 排列算法
 */
function permutation(arr, num){
	var r=[];
	(function f(t,a,n){
		if (n==0) return r.push(t);
		for (var i=0,l=a.length; i<l; i++){
			f(t.concat(a[i]), a.slice(0,i).concat(a.slice(i+1)), n-1);
		}
	})([],arr,num);
	return r;
}
//是否有重复值
  function isRepeat(arr){  
      
         var hash = {};  
      
         for(var i in arr) {  
      
             if(hash[arr[i]])  
      
                  return true;  
      
             hash[arr[i]] = true;  
      
         }  
      
         return false;  
      
    }  

var re = result(bet,kj,ws);
var rr = isRepeat(bet);
console.log(re);
var code=[];
code[0]="1 2";
code[1]="3";
code=code.join(',');
code=code.split(',');
//console.log(code);

 var a="a,b,c,d,e,f"; var b=a.split(",");
 //console.log(a.split(',')[0]);
/**
 * 组合算法
 *
 * @params Array arr		备选数组
 * @params Int num
 *
 * @return Array			组合
 *
 * useage:  combine([1,2,3,4,5,6,7,8,9], 3);
 */
function combine(arr, num) {
	var r = [];
	(function f(t, a, n) {
		if (n == 0) return r.push(t);
		for (var i = 0, l = a.length; i <= l - n; i++) {
			f(t.concat(a[i]), a.slice(i + 1), n - 1);
		}
	})([], arr, num);
	return r;
}

/**
 * 排列算法
 */
function permutation(arr, num){
	var r=[];
	(function f(t,a,n){
		if (n==0) return r.push(t);
		for (var i=0,l=a.length; i<l; i++){
			f(t.concat(a[i]), a.slice(0,i).concat(a.slice(i+1)), n-1);
		}
	})([],arr,num);
	return r;
}


http.createServer(function(request, response) {
  response.writeHead(200, {"Content-Type": "text/plain"});
  response.write("Hello World 2");
  response.end();
}).listen(8888);
