export default class Utility {

  /**
   * Get largest filesize (if more then only one is selected)
   *
   * @param field
   * @returns {number}
   */
  static getLargestFileSize(field) {
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
  static isFileExtensionInList(extension, list) {
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
  static getExtensionFromFileName(fileName) {
    return fileName.split('.').pop().toLowerCase();
  };

  /**
   * Get uri without get params
   *
   * @param {string} uri
   * @returns {string}
   */
  static getUriWithoutGetParam(uri) {
    const parts = uri.split('?');
    return parts[0];
  };

  /**
   * Get random string
   *
   * @param {int} length
   * @returns {string}
   */
  static getRandomString(length) {
    let text = '';
    const possible = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    for (let i = 0; i < length; i++) {
      text += possible.charAt(Math.floor(Math.random() * possible.length));
    }
    return text;
  };

  /**
   * Redirect to an external or internal target
   *
   * @param {string} redirectUri
   */
  static redirectToUri(redirectUri) {
    if (redirectUri.indexOf('http') !== -1) {
      window.location = redirectUri;
    } else {
      window.location.pathname = redirectUri;
    }
  };

  static hideElement(element) {
    if (element !== null) {
      element.style.display = 'none';
    }
  }

  static showElement(element) {
    if (element !== null) {
      element.style.display = 'block';
    }
  }

  /**
   * Check if an element is really visible (not only check for style visibility)
   *
   * @param element
   * @returns {boolean}
   */
  static isElementVisible(element) {
    return element.offsetParent !== null;
  }
}
