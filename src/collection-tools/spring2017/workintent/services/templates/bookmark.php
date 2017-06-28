<html>
<head>
  <title>Bookmark</title>
  <link href="../styles.css" rel="stylesheet" type="text/css" />
  <script src="../lib/jquery-2.1.3.min.js"></script>
  <link href="../lib/select2/select2.css" rel="stylesheet" type="text/css" />
  <script src="../lib/select2/select2.full.min.js"></script>
  <style>
  #tag-input {
    width: 500px;
  }
  </style>
</head>
  <body class="body" onload="document.f.annotation.focus();">
    <br/><center>
    <form name="f" action="saveBookmarkAux.php" method=POST>
      <table class="body" width=90%>
        <tr><th>Bookmark the following page: <a href="<?php echo $originalURL ?>"><?php echo $title ?></a><br/><br/></th></tr>
        <?php

        if($instructorID==1){
          ?>
          <tr>
            <td align=center><em>What is useful about this source? How would you use it in writing your paper?</em>
            <br/>
            <textarea cols=35 rows=6 name="annotation"></textarea>
            </td>
            </tr>

          <?php
        }else{
          ?>
          <tr>
            <td align=center><span style="font-size:10pt"><em>What specific information from this source will you use in your report?</em></span>
            <br/>
            <textarea cols=35 rows=3 name="useful_info"></textarea>
            </td>
            </tr>

            <tr>
              <td align=center><span style="font-size:10pt"><em>What qualifications does the author have as evidence of expertise or trustworthiness?</em></span>
              <br/>
              <textarea cols=35 rows=3 name="author_qualifications"></textarea>
              </td>
              </tr>
              <?php
        }


          ?>

          <input type="hidden" name="originalURL" value="<?php echo $originalURL ?>"/>
          <input type="hidden" name="source" value="<?php echo $url; ?>"/>
          <input type="hidden" name="host" value="<?php echo $host; ?>"/>
          <input type="hidden" name="title" value="<?php echo $title; ?>"/>
          <input type="hidden" name="site" value="<?php echo $site; ?>"/>
          <input type="hidden" name="queryString" value="<?php echo $queryString; ?>"/>
        <input type="hidden" name="localDate" value="<?php echo $localDate; ?>"/>
        <input type="hidden" name="localTime" value="<?php echo $localTime; ?>"/>
        <input type="hidden" name="localTimestamp" value="<?php echo $localTimestamp; ?>"/>
        <tr><td align=center><span style="font-size:10pt">

          <?php
          if($instructorID==1){
            echo "How good is this page? Rate it:";
          }else{
            echo "How useful is this source? Rate it:";
          }

          ?>
        </span>
          </td></tr></table>
          <table><tr><td><input type="radio" name="rating" value="1"></td>
            <td><input type="radio" name="rating" value="2"></td>
            <td><input type="radio" name="rating" value="3"></td>
            <td><input type="radio" name="rating" value="4"></td>
            <td><input type="radio" name="rating" value="5"></td></tr>
            <tr align=center><td><span style="font-size:10pt">1</span></td>
              <td><span style="font-size:10pt">2</span></td>
                <td><span style="font-size:10pt">3</span></td>
                  <td><span style="font-size:10pt">4</span></td>
                    <td><span style="font-size:10pt">5</span></td></tr>
          </table>

          <label><span style="font-size:10pt">Add tags (press <u>enter</u> after every tag)</span></label>
          <select name="tags[]" id="tag-input" multiple="multiple">
            <?php
            //show all user tags
            foreach($available_tags as $tag){
              printf("<option value='%s'>%s</option>", $tag["name"], $tag["name"]);
            }
            ?>
          </select>

          <table>
            <tr>
              <td align=center><br><input type="submit" value="Save" /> <input type="button" value="Cancel" onclick="window.close();" /></td>
              </tr>
          </table>

        </table>
      </form>

      <script>
      var previous_tags = $("#tag-input").val() || [];
      $("#tag-input").select2({
        tags: true
      }).on("change", function(el){
        var changeType = ""; //add or remove
        var changeTag = "";
        var current_tags = $(this).val();
        for(var i = 0; i < current_tags.length; i++){
          var t = current_tags[i];
          var loc = previous_tags.indexOf(t);
          if(loc != -1){
            previous_tags.splice(loc, 1);
          } else {
            //added new tag
            changeType = "add";
            changeTag = t;
            break;
          }
        }
        if(changeType == ""){
          //ought to be exactly one tag left
          changeType = "remove";
          if(previous_tags.length == 1){
            changeTag = previous_tags[0];
          }
        }
        previous_tags = current_tags;
        changeTag = changeTag.trim();
        if(changeType != "" && changeTag != ""){
          //send ajax request to add action TODO
          $.ajax({
            url: "insertAction.php",
            type: "GET",
            data : {
              "action" : "tag_" + changeType,
              "value" : changeTag
            },
            success: function(){
              console.log(changeTag, changeType);
              console.log("Recorded");
            }
          })
        }
      });
      </script>
    </body>
</html>
