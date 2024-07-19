/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */

/**
 * テーブルのフォーカス行にハイライト表示にする関数をテーブルの行に仕込む.
 */
function _initTableRollovers(element, id) {
	if (!document.getElementById) return
	
	var table_elem = document.getElementById(id);
	var table_bodies = table_elem.tBodies;
	
	for (var i = 0; i < table_bodies.length; i++) {
		var rows = table_bodies[i].rows;
		for (var j = 0; j < rows.length; j++) {
			rows[j].onmouseover = function() {
				this.style["background-color"] = "#FEA";
			};
			rows[j].onmouseout = function() {
				this.style["background-color"] = "";
			};
		}
	}
}

/**
 * _initTableRolloversに引数を渡す.
 * @param id
 * @returns {Function}
 */
function initTableRollovers(id) {
	return function(element) {
		_initTableRollovers(element, id);
	};
}

// Initialize Sample.
// try{
// 	$(window).addEventListener("load",initTableRollovers('any_id'),false);
// }catch(e){
// 	$(window).attachEvent("onload",initTableRollovers('any_id'));
// }


