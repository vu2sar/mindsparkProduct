var simpleSpell = {};

simpleSpell.areSimilar = function(word1, word2) {
word1 = word1.trim();
word2 = word2.trim();
word1_length = word1.length;
word2_length = word2.length;
if(word1 == word2)
  return {"result":1, "reason": "Words are the same."};

var word1_metaphone = double_metaphone(word1);
var word2_metaphone = double_metaphone(word2);
console.log(word1_metaphone);
console.log(word2_metaphone);
if(word1_metaphone.primary == word2_metaphone.primary && word1_metaphone.secondary == word2_metaphone.secondary) //words sound similar
  return {"result":1, "reason": "Words have the same sound."};



word1_word2_editDistance = getDameraulevenshteinDistance(word1, word2);
if(word1_word2_editDistance == 1) {
  if(word1_length == word2_length)
    return checkForQwertyInsertEdit(word1, word2);
  return {"result":1, "reason": "Within 1 edit distance."};
}


return {"result":0, "reason": "Not same at all!"};

};

function checkForQwertyInsertEdit(word1, word2) {
  var smallLength = word1.length;
  var qwertyObject = 
    {
      'a': ['q', 'w', 's', 'x', 'z'],
      'b': [ 'v', 'f', 'g', 'h', 'n'],
      'c': [ 'x', 's', 'd', 'f', 'v'],
      'd': [ 'x', 's', 'e', 'r', 'f', 'c'],
      'e': [ 's', 'w', 'r', 'f', 'd'],
      'f': [ 'c', 'd', 'r', 't', 'g', 'v'],
      'g': [ 'v', 'f', 't', 'y', 'h', 'b'],
      'h': [ 'b', 'g', 'y', 'u', 'j', 'n'],
      'i': [ 'j', 'u', 'o', 'l', 'k'],
      'j': [ 'n', 'h', 'u', 'i', 'k', 'm'],
      'k': [ 'm', 'j', 'i', 'o', 'l'],
      'l': [ 'k', 'o', 'p'],
      'm': [ 'n', 'h', 'j', 'k'],
      'n': [ 'b', 'g', 'h', 'j', 'm'],
      'o': [ 'k', 'i', 'p', 'l'],
      'p': [ 'l', 'o'],
      'q': [ 'w', 's', 'a'],
      'r': [ 'd', 'e', 't', 'g', 'f'],
      's': [ 'z', 'a', 'w', 'e', 'd', 'x'],
      't': [ 'f', 'r', 'y', 'h', 'g'],
      'u': [ 'h', 'y', 'i', 'k', 'j'],
      'v': [ 'c', 'd', 'f', 'g', 'b'],
      'w': [ 'a', 'q', 'e', 'd', 's'],
      'x': [ 'z', 'a', 's', 'd', 'c'],
      'y': [ 'g', 't', 'u', 'j', 'h'],
      'z': [ 'a', 's', 'x']
};
  //need to check for transpose position
  for(i = 0; i < smallLength; i++) {
    if(word1.charAt(i) != word2.charAt(i)) {
      if(i < smallLength - 1 && word1.charAt(i+1) != word2.charAt(i+1))
        return {"result":1, "reason": "Edit distance 1 and chars are transposed."};
      break;
    }
  }
  console.log(i);
  if(i < smallLength) {
    originalChar = word1.charAt(i);
    substitutedChar = word2.charAt(i);
    console.log(originalChar);
    console.log(substitutedChar);
    console.log($.inArray(substitutedChar, qwertyObject[originalChar]))
    if($.inArray(substitutedChar, qwertyObject[originalChar]) > -1)
      return {"result":1, "reason": "Words within 1 edit distance and the substituted char is in near the original char on a qwerty keyboard."};
    return {"result":0, "reason": "Edit distance 1 but the substituted char is not near the original char on qwerty keyboard."};
  }
}

function getEditDistance(a, b) {
  if(a.length === 0) return b.length; 
  if(b.length === 0) return a.length; 
 
  var matrix = [];
 
  // increment along the first column of each row
  var i;
  for(i = 0; i <= b.length; i++){
    matrix[i] = [i];
  }
 
  // increment each column in the first row
  var j;
  for(j = 0; j <= a.length; j++){
    matrix[0][j] = j;
  }
 
  // Fill in the rest of the matrix
  for(i = 1; i <= b.length; i++){
    for(j = 1; j <= a.length; j++){
      if(b.charAt(i-1) == a.charAt(j-1)){
        matrix[i][j] = matrix[i-1][j-1];
      } else {
        matrix[i][j] = Math.min(matrix[i-1][j-1] + 1, // substitution
                                Math.min(matrix[i][j-1] + 1, // insertion
                                         matrix[i-1][j] + 1)); // deletion
      }
    }
  }
 
  return matrix[b.length][a.length];
}

//based on: http://en.wikibooks.org/wiki/Algorithm_implementation/Strings/Levenshtein_distance
//and:  http://en.wikipedia.org/wiki/Damerau%E2%80%93Levenshtein_distance
function getDameraulevenshteinDistance( a, b )
{
  var i;
  var j;
  var cost;
  var d = new Array();
 
  if ( a.length == 0 )
  {
    return b.length;
  }
 
  if ( b.length == 0 )
  {
    return a.length;
  }
 
  for ( i = 0; i <= a.length; i++ )
  {
    d[ i ] = new Array();
    d[ i ][ 0 ] = i;
  }
 
  for ( j = 0; j <= b.length; j++ )
  {
    d[ 0 ][ j ] = j;
  }
 
  for ( i = 1; i <= a.length; i++ )
  {
    for ( j = 1; j <= b.length; j++ )
    {
      if ( a.charAt( i - 1 ) == b.charAt( j - 1 ) )
      {
        cost = 0;
      }
      else
      {
        cost = 1;
      }
 
      d[ i ][ j ] = Math.min( d[ i - 1 ][ j ] + 1, d[ i ][ j - 1 ] + 1, d[ i - 1 ][ j - 1 ] + cost );
      
      if(
         i > 1 && 
         j > 1 &&  
         a.charAt(i - 1) == b.charAt(j-2) && 
         a.charAt(i-2) == b.charAt(j-1)
         ){
          d[i][j] = Math.min(
            d[i][j],
            d[i - 2][j - 2] + cost
          )
         
      }
    }
  }
 
  return d[ a.length ][ b.length ];
}


