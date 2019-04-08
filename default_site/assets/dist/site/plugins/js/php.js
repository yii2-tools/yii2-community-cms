
/* php port start */

function in_array( needle, haystack, strict )
{
    var found = false, key, strict = !!strict;

    for (key in haystack) {
        if ( ( strict && haystack[ key ] === needle ) || ( !strict && haystack[ key ] == needle ) ) {
            found = true;
            break;
        }
    }

    return found;
}

function count( mixed_var, mode )
{
    var key, cnt = 0;

    if ( mode == 'COUNT_RECURSIVE' ) {
        mode = 1; }

    if ( mode != 1 ) {
        mode = 0; }

    for (key in mixed_var) {
        ++cnt;

        if ( mode == 1 && mixed_var[ key ] && ( mixed_var[ key ].constructor === Array || mixed_var[ key ].constructor === Object ) ) {
            cnt += count(mixed_var[ key ], 1); }
    }

    return cnt;
}

function explode( delimiter, string )
{
    var emptyArray = {0: ''};

    if ( arguments.length != 2
        || typeof arguments[0] == 'undefined'
        || typeof arguments[1] == 'undefined' ) {
        return null;
    }

    if ( delimiter === ''
        || delimiter === false
        || delimiter === null ) {
        return false;
    }

    if ( typeof delimiter == 'function'
        || typeof delimiter == 'object'
        || typeof string == 'function'
        || typeof string == 'object' ) {
        return emptyArray;
    }

    if ( delimiter === true ) {
        delimiter = '1';
    }

    return string.toString().split(delimiter.toString());
}

function substr( string, indexA, indexB )
{
    return string.substring(indexA, indexB);
}

function substr_replace( str, replace, start, length )
{
    if (start < 0) { // start position in str
        start = start + str.length;
    }

    length = length !== undefined ? length : str.length;

    if (length < 0) {
        length = length + str.length - start;
    }

    return str.slice(0, start) + replace.substr(0, length) + replace.slice(length) + str.slice(start + length);
}

function strlen( string )
{
    return string.length;
}

function strpos( haystack, needle, offset )
{
    var i = haystack.indexOf(needle, offset);
    return i >= 0 ? i : false;
}

function strrpos( haystack, needle, offset)
{
    var i = haystack.lastIndexOf(needle, offset); // returns -1
    return i >= 0 ? i : false;
}

function decbin( number )
{
    if ( number < 0 ) {
        number = 0xFFFFFFFF + number + 1; }

    return parseInt(number, 10).toString(2);
}

function bindec( binary_string )
{
    binary_string = ( binary_string + '' ).replace(/[^01]/gi, '');
    return parseInt(binary_string, 2);
}

function utf8_encode( str_data )
{
    str_data = str_data.replace(/\r\n/g, "\n");

    var utftext = "";

    for (var n = 0; n < str_data.length; n++) {
        var c = str_data.charCodeAt(n);

        if ( c < 128 ) {
            utftext += String.fromCharCode(c);
        } else if ( ( c > 127 ) && ( c < 2048 ) ) {
            utftext += String.fromCharCode(( c >> 6 ) | 192);
            utftext += String.fromCharCode(( c & 63 ) | 128);
        } else {
            utftext += String.fromCharCode(( c >> 12 ) | 224);
            utftext += String.fromCharCode(( ( c >> 6 ) & 63 ) | 128);
            utftext += String.fromCharCode(( c & 63 ) | 128);
        }
    }

    return utftext;
}

function htmlspecialchars(string, quote_style, charset, double_encode)
{
    var optTemp = 0,
        i = 0,
        noquotes = false;
    if (typeof quote_style === 'undefined' || quote_style === null) {
        quote_style = 2;
    }
    string = string.toString();
    if (double_encode !== false) { // Put this first to avoid double-encoding
        string = string.replace(/&/g, '&amp;');
    }
    string = string.replace(/</g, '&lt;').replace(/>/g, '&gt;');

    var OPTS = {
        'ENT_NOQUOTES': 0,
        'ENT_HTML_QUOTE_SINGLE': 1,
        'ENT_HTML_QUOTE_DOUBLE': 2,
        'ENT_COMPAT': 2,
        'ENT_QUOTES': 3,
        'ENT_IGNORE': 4
    };
    if (quote_style === 0) {
        noquotes = true;
    }
    if (typeof quote_style !== 'number') { // Allow for a single string or an array of string flags
        quote_style = [].concat(quote_style);
        for (i = 0; i < quote_style.length; i++) {
            // Resolve string input to bitwise e.g. 'ENT_IGNORE' becomes 4
            if (OPTS[quote_style[i]] === 0) {
                noquotes = true;
            } else if (OPTS[quote_style[i]]) {
                optTemp = optTemp | OPTS[quote_style[i]];
            }
        }
        quote_style = optTemp;
    }
    if (quote_style & OPTS.ENT_HTML_QUOTE_SINGLE) {
        string = string.replace(/'/g, '&#039;');
    }
    if (!noquotes) {
        string = string.replace(/"/g, '&quot;');
    }

    var additionalSpecialChars =
    {
        "'" : "&#39;",
        "{" : "&#123;",
        "}" : "&#125;",
        "№" : "&#8470;",
        "~" : "&#126;",
        "`" : "&#96;"
    }, x;

    for (x in additionalSpecialChars) {
        string = string.replace(new RegExp(x, "g"), additionalSpecialChars[x]); }

    string = string.replace(/\//g, "&#47;");
    string = string.replace(/\[/g, "&#91;");
    string = string.replace(/\\/g, "&#92;");
    string = string.replace(/\]/g, "&#93;");

    return string;
}

/* php port end */
