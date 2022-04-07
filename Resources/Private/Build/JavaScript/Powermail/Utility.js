export default class Utility {

  /**
   * Get largest filesize (if more then only one is selected)
   *
   * @param field
   * @returns {number}
   */
  static getLargestFileSize = function(field) {
    let size = 0;
    for (let i = 0; i < field.files.length; i++) {
      let file = field.files[i];
      if (file.size > size) {
        size = file.size;
      }
    }
    return size;
  }

  /**
   * Check if fileextension is allowed in dotted list
   * 		"jpg" in ".jpg,.jpeg" => true
   * 		"jpg" in ".gif,.png" => false
   *
   * @param {string} extension
   * @param {string} list
   * @returns {boolean}
   */
  static isFileExtensionInList = function(extension, list) {
    return list.indexOf('.' + extension) !== -1;
  };

  /**
   * Get extension from filename in lowercase
   * 		image.jpg => jpg
   * 		image.JPG => jpg
   *
   * @param {string} fileName
   * @returns {string}
   */
  static getExtensionFromFileName = function(fileName) {
    return fileName.split('.').pop().toLowerCase();
  };
}
