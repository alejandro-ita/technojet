/**
 * Eliminación de caracteres de un String
 * @url https://www.sitepoint.com/trimming-strings-in-javascript/
 */

/**Elimina los caracteres del comienzo de la cadena**/
String.prototype.trimLeft = function(charlist) {
  if (charlist === undefined)
    charlist = "\s";

  return this.replace(new RegExp("^[" + charlist + "]+"), "");
};

/**Elimina los caracteres del final de la cadena**/
String.prototype.trimRight = function(charlist) {
  if (charlist === undefined)
    charlist = "\s";

  return this.replace(new RegExp("[" + charlist + "]+$"), "");
};

/**Elimina caracteres de ambos extremos**/
/**se renombró la función ya que se genera error al llamar swal**/
String.prototype.strim = function(charlist) {
  return this.trimLeft(charlist).trimRight(charlist);
};

/**Reemplaza todas las apariciones del string buscado con el string de reemplazo**/
String.prototype.str_replace = function(search, replace) {
	if (search === undefined || replace === undefined) {
		console.warn('No se ha definido el valor a buscar y el valor a reemplazar en la cadena ' + this);
		return this;
	}

	if (search.constructor == String/* && replace.constructor == String*/) {
		search	= [search];
		replace = [replace];
	}

	var str = this;
	if (search.constructor == Array && replace.constructor == Array && search.length == replace.length) {
		search.forEach(function(value, index) {
			str = str.replace(new RegExp(value, "g"), replace[index]);
		});
	}

	return str;
}