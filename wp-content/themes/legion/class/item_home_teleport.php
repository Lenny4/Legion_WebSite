<?php
/**
 * Created by PhpStorm.
 * User: alexa
 * Date: 15/11/2017
 * Time: 20:26
 */

include_once("item_home.php");
include_once("map.php");

class item_home_teleport extends item_home
{
    function displayHome()
    {
        $return = "";
        $return .= '
        <a class="pinterest" onclick="showMoreItemHome(\'' . get_class($this) . '\')">
            <li class="list-group-item col-sm-6 col-xs-12">
        <div class="display_item noPadding">
        ';
        $return .= wp_get_attachment_image($this->image, 'large', false, array("class" => "img-responsive center-block", "style" => "z-index:-1;width:90%;"));
        $return .= '<p class="message hidden-xs" style="width: 50%;background-image: url(\'' . wp_get_attachment_image_src(132, "full")[0] . '\') ">
                    <span>' . $this->name . '</span>
                    </p>
                    <p class="hidden-sm hidden-md hidden-lg Quickstyle text-center">' . $this->name . '</p>';
        $return .= '</div></li></a>';
        return $return;
    }

    public function show()
    {
        $return = "<div class='col-sm-9 col-xs-12'>";
        if (isWowAdmin()) {
            $req = $GLOBALS["dbh"]->query("SELECT * FROM `item_home` WHERE `phpclasse`='" . get_class($this) . "'");
            while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
                $currentTeleportPrice = $data["price"];
            }
            $return .= '<form id="changePriceTeleport" method="post">
  <div class="form-group">
    <label for="price">Current teleportation price</label>
    <input name="price" type="number" value="' . $currentTeleportPrice . '" class="form-control" id="price">
  </div>
  <button type="submit" class="btn btn-default">Change</button>
</form> ';
        }
        $return .= '<div class="alert alert-success alert-dismissable">
  <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
  <strong>' . get_field("message_teleportation", $GLOBALS["shop_page_id"]) . '</strong>
</div>';
        $return .= '<div style="display: inline-block; position: relative" id="display-maps">' . $_SESSION["map"]->display(null, 'map') . '</div>';
        $return .= '<div style="display: inline-block; width: 100%;" id="display-maps-option">' . $_SESSION["map"]->display(null, 'option') . '</div>';
        if (isWowAdmin()) {
            $return .= '<hr style="margin-top: 0px"/><p>Add map <a href="http://www.wowhead.com/maps">All Maps</a></p>
 <form method="post" id="addMapTeleportation">
 <div class="form-group col-sm-6">
    <label for="previous_id">Parent map id</label>
    <input name="previous_id" type="number" min="1" class="form-control" id="previous_id">
  </div>
  <div class="form-group col-sm-6">
    <label for="name">Name (optionnal if cannot tp on it)</label>
    <input name="name" type="text" class="form-control" id="name">
  </div>
  <div class="form-group col-sm-6">
    <label for="image">Url of the image of the map</label>
    <input name="image" type="text" class="form-control" id="image">
  </div>
  <div class="form-group col-sm-6">
      <label for="city">Is it a city</label>
      <select name="city" class="form-control" id="city">
        <option selected value="0">No</option>
        <option value="1">Yes</option>
      </select>
  </div> 
  <div class="form-group col-sm-6">
    <label for="min_level">Min level</label>
    <input name="min_level" type="number" min="1" max="110" class="form-control" id="min_level">
  </div>
  <div class="form-group col-sm-6">
    <label for="max_level">Max level</label>
    <input name="max_level" type="number" min="1" max="110" class="form-control" id="max_level">
  </div>
  <div class="form-group col-sm-6">
      <label for="can_tp">Can teleport here</label>
      <select name="can_tp" class="form-control" id="can_tp">
        <option selected value="0">No</option>
        <option value="1">Yes</option>
      </select>
  </div> 
  <button type="submit" class="btn btn-default btn-block">Submit</button>
</form>';

        }
        $return .= "</div>";
        return $return;
    }
}