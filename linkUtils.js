/* link management code, copyright 2000 john manoogian III, jm3@jm3.net
 * this makes me fucking HAPPY. i haven't coded anything fun in a while
 *
 *
 * permission to use, copy, modify, distribute, and sell this software and its
 * documentation for any purpose is hereby granted without fee, provided that
 * the above copyright notice appear in all copies and that both that
 * copyright notice and this permission notice appear in supporting
 * documentation.  no representations are made about the suitability of this
 * software for any purpose.  it is provided "as is" without express or 
 * implied warranty.
 */

/* Link object constructor */
function Link( arg_url, arg_desc, arg_why ) {
  /* data */
  this.url  = arg_url;
  this.desc = arg_desc;
  this.why  = arg_why;

  /* methods */
  this.draw    = draw;
}

/* Link instance methods: */
function draw( arg_imgOpts ) {
  var url  = this.url;
  var name = "" + Math.random();
  /* this next could fail, but it's not likely. */
  name = "linkImg_" + name.substring( 2, name.length - 1); 
  
  /* we allow sloppy urls, and then clean them up here before launching */
  if( url ) {
    if( url.indexOf( "http://" ) != 0 &&
        url.indexOf( "ftp://" ) != 0 )
      url = "http://" + url;
  }

  return "<a href=\"" + url + "\" " + 
  "onMouseOver=\"javascript:showLink(" +
  "'" + name      + "'," +
  "'" + this.url  + "'," +
  "'" + this.why  + "'," +
  "'" + this.desc + "'"  +
  ");\" " +
  
  "onMouseOut=\"javascript:rollOffLink(" +
  "'" + name + "'" +
  ");\">" + 
  "<img name='" + name + "' " + arg_imgOpts + " src=\"images/button2.gif\">" +
  "</a>";
}


/* * * * * * * * * * * * * * * * * * * * * */

/* Link object helper methods: */

/* populates the info fields with the information about this link and
 * calls rollOverLink to highlight the link
 */
 
function showLink( arg_linkName, arg_linkURL, arg_linkWhy, arg_linkDesc ) {
  rollOverLink( arg_linkName );
  if(arg_linkURL != "undefined" )
    f.url.value  = arg_linkURL;
  else
    f.url.value  = "";
    
  if(arg_linkWhy != "undefined" )
    f.why.value  = arg_linkWhy;
  else
    f.desc.value  = "";

  if(arg_linkDesc != "undefined" )
    f.desc.value  = arg_linkDesc;
  else
    f.desc.value  = "";
}

/* highlights the link image */ 
function rollOverLink( arg_linkImgName) {
  if( document.images )
    if( window.document[arg_linkImgName].src ) {
      window.document[arg_linkImgName].src = "images/button2_f2.gif";
      } else
        alert( "rollOverLink( arg_linkImgName ) failed" );
}

/* un-highlights the link image */
function rollOffLink( arg_linkImgName ) {
  if( document.images )
    if( window.document[arg_linkImgName].src ) {
      window.document[arg_linkImgName].src = "images/button2.gif";
      } else
        alert( "rollOverLink( arg_linkImgName ) failed" );
}

/* draw a grid of links, given a 1dimensional array of Link objects.
 * adjust the size of the grid to remain square no matter how large
 * the array. draws the individual links by invoking Link.draw()
 */ 
function drawMatrix( arg_links, arg_tableOpts, arg_imgOpts ) {
  if( arg_links && arg_links.length ) {
    /* locals */
    var size    = arg_links.length;
    var rows    = 0;
    var cols    = 0;
    var linkNum = 0;
    var table   = "";

    if( size < 9 )
      size = 9;
    else if( size > 4000 ) {
      alert( size + " is too many links. change the code or relax, dude." );
      return;
    }

    /* this should give us a squarish box which will be slightly
     * wider than tall for non perfect-square numbers
     */
    cols = rows = Math.round( Math.sqrt( size ));
    if( cols != Math.sqrt( size )) {
      cols++;
    }

    /* alert( "will draw a grid " + cols + " wide x " + rows + " high." );  DEBUG */
    var danger = 40; /* debug; catch overflow/runaways */
    
    table += "<table " + arg_tableOpts + ">\n";
    for( i = 0; i<rows; i++ ) {
      if( linkNum > danger ) { alert( "j: " + j + ", i:" + i + " overflow in outer loop" ); return; }

      table += "  <tr>\n" ;
      for( j = 0; j<cols; j++ ) {
        if( linkNum > danger ) { alert( "j: " + j + ", i:" + i + " overflow in inner loop" ); return; }
        table += "    <td>";
        if( links[linkNum] )
          table += links[linkNum].draw( arg_imgOpts );
        else /* do some kind of no-op here */
          table += "&nbsp;";
        linkNum++;
        table += "</td>\n";
      } /* end inner for loop [cols] */
      table += "</tr>\n";
    } /* end outer for loop [rows] */
    table += "</table>\n";

    document.write( table );
    /* alert( table ); // debug */

  }
}

/* * * * * * * * * * * * * * * * * * * * * */
