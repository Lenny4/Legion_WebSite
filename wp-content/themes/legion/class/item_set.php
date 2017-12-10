<?php

include_once("parent_item.php");

class item_set extends parent_item
{
    public $item_set_id = null;
    public $setBonuses = null;
    public $items = null;
    public $price = 0;
    public $item_classes = null;
    public $allowableClasses = null;
    public $vote = 0;


    public function hydrateAPI($data)
    {
        if (isset($data->id)) {
            $this->item_set_id = $data->id;
        }
        if (isset($data->name)) {
            $this->name = $data->name;
        }
        if (isset($data->setBonuses)) {
            $this->setBonuses = $data->setBonuses;
        }
        if (isset($data->items)) {
            $this->items = $data->items;
        }
    }

    function display($display_option = false, $display_admin = false)
    {
        $return = '<div class="display_item_set display_item" style="padding-left: 20px;padding-right: 20px">';
        $return .= '<p class="item_id"><span class="item_id">Item_id </span><span class="value"><a target="_blank" href="http://www.wowhead.com/item-set=' . $this->item_set_id . '">' . $this->item_set_id . ' <i class="fa fa-info-circle" aria-hidden="true"></i></a></span></p>';
        $i = 0;
        $isClose = true;
        foreach ($this->items as $itemID) {
            if ($i % 2 == 0) {
                $return = $return . "<div class='row'>";
                $isClose = false;
            }
            $return = $return . previewItem($itemID, '', $this->vote, true, true);
            if ($i % 2 != 0) {
                $return = $return . "</div>";
                $isClose = true;
            }
            $i++;
        }
        if ($isClose == false) {
            $return = $return . "</div>";
        }
        if ($display_option == true) {
            $votePoints = $this->getVotePoint();
            $buyPoints = $this->getBuyPoint();
            $return = $return . '<div><p class="price_buy_points"><span class="price_buy_points">' . ucfirst('price_buy_points') . ' </span><span class="value">' . $buyPoints . wp_get_attachment_image(168, 'thumbnail', true, ["class" => "img-responsive", "style" => "width:20px;float:right;"]) . '</span></p>';
            if ($this->vote == 1) {
                $return = $return . '<p style="margin-right: 10px;" class="price_vote_points"><span class="price_vote_points">' . ucfirst('price_vote_points') . ' </span><span class="value">' . $votePoints . wp_get_attachment_image(169, 'thumbnail', true, ["class" => "img-responsive", "style" => "width:20px;float:right;"]) . '</span></p></div>';
            }
            $return = $return . "<div style='width: 1px;height: 10px'></div>";
            $return = $return . "<div class='option'><div id='result_req_user_item'></div>
    <button style='float:left;' onclick=\"addToCart(this," . $this->item_set_id . ",'item_set')\" type=\"button\" class=\"btn btn-success\">" . wp_get_attachment_image(221, 'thumbnail', true, array('class' => 'img-responsive')) . "</button>";
            if (($this->promotion > 0 AND $this->promotion <= 100) AND $this->time_promotion > time()) {
                $return = $return . "<div style='display: inline-block;float: left;margin-left:15px; position: relative'>";
                $return = $return . wp_get_attachment_image(209, 'thumbnail', true, array('class' => 'img-responsive'));
                $return = $return . "<span style='transform: inherit;top: inherit;right: inherit;font-size: inherit;' class='promo'>-" . $this->promotion . "%</span>";
                $return = $return . "</div>";
            }
            $return = $return . "<div style='display: inline-block;float: left;margin-left:15px; position: relative; width:90px'>";
            $return = $return . wp_get_attachment_image(222, 'thumbnail', true, array('class' => 'img-responsive'));
            $return = $return . "<span style='top: inherit;right: inherit;transform: inherit;left: 25px;bottom: 20px;' class='promo'>-30%</span>";
            $return = $return . "</div>";
            $return = $return . "</div>";
        }
        if ($display_admin == true AND isWowAdmin()) {
            $return = $return . "<div class='col-xs-12' style='padding: 0'>";
            $return = $return . '<hr><div id="ajaxLoaderShopAdmin"></div><div id="result_req_admin_item"></div>
             <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#update_promotion_admin">Promotion</button>
             <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#update_item_admin">Item set</button>
                <div id="update_promotion_admin" class="collapse">
                <button onclick="removePromotion(' . $this->item_set_id . ',true)" type="button" class="btn btn-danger">Remove promotion</button>
                <p>Update promotion</p>
                <form method="post" id="update_promotion_item_admin">
                    <input name="item_set_id" class="hidden" value="' . $this->item_set_id . '" />
                    <input name="pourcent" placeholder="poucentage ex:20" type=\'number\' min="0" max="100" />
                    <input name="date" type=\'date\' placeholder=\'dd/mm/yyyy\' />
                    <button type="submit" class="btn btn-info">Update</button>
                </form>
                </div>
                <div id="update_item_admin" class="collapse">
                <button onclick="removeItem(' . $this->item_set_id . ',true)" type="button" class="btn btn-danger">Remove Item set</button>
                </div> 
            </div>';
        }
        $return = $return . '</div>';
        return $return;
    }

    function smallDisplay($showBuy = false, $directBuy = false, $selectThisDirectBuy = false, $requiredLevel = null)
    {
        $return = '';
        $allInfos = $this->getAllItemInfoOfTheSet($this->items);
        $canDisplayThisItem = true;
        foreach ($allInfos["requiredLevel"] as $level) {
            if ($level != $requiredLevel AND $requiredLevel != null) {
                $canDisplayThisItem = false;
                break;
            }
        }
        if ($allInfos == null OR $allInfos == false OR $canDisplayThisItem == false) {

        } else {
            $dataShow["value"] = $this->item_set_id;
            $dataShow["id"] = "previewItemSet";
            $json = json_encode($dataShow);
            $globalItemLevel = intval(array_sum($allInfos["itemLevel"]) / sizeof($allInfos["itemLevel"]));
            if ($directBuy == true) {
                $return = $return . "<div class='col-xs-12 noPadding' style='margin-bottom: 20px;'><a class='pinterest' data-show='" . $json . "' onclick='showMoreShop(this)'><li class='list-group-item col-sm-11 col-xs-12 noPadding'><div class='display_item display_item_small radius-right-no'>";
            } else {
                $return = $return . "<a class='pinterest' data-show='" . $json . "' onclick='showMoreShop(this)'><li class='list-group-item col-sm-4 col-xs-12'><div class='display_item display_item_small'>";
            }
            if ($this->promotion > 0 AND $this->promotion <= 100 AND $this->time_promotion > time()) {
                $return = $return . wp_get_attachment_image(209, 'thumbnail', true, array('class' => 'img-responsive promo'));
                $return = $return . "<span class='promo'>-" . $this->promotion . "%</span>";
            }
            foreach ($allInfos["icon"] as $oneImage) {
                $return = $return . '<img src="https://wow.zamimg.com/images/wow/icons/large/' . $oneImage . '.jpg" alt="' . $oneImage . '">';
            }
            $return = $return . '<p class="name"><span class="name">Name </span><span class="value">"' . $this->name . '"</span></p>';
            $return = $return . '<p class="itemLevel"><span class="itemLevel">Average Item Level </span><span class="value">' . $globalItemLevel . '</span></p>';
            if ($directBuy == true) {
                $votePoints = $this->getVotePoint(false, false, true);
                $buyPoints = $this->getBuyPoint(false, false, true);
            } else {
                $votePoints = $this->getVotePoint();
                $buyPoints = $this->getBuyPoint();
            }
            $return = $return . '<div class="display_price"><p class="price_buy_points"><span class="price_buy_points">' . ucfirst('price_buy_points') . ' </span><span class="value">' . $buyPoints . wp_get_attachment_image(168, 'thumbnail', true, ["class" => "img-responsive", "style" => "width:20px;float:right;"]) . '</span></p>';
            if ($this->vote == 1) {
                $return = $return . '<p style="margin-right: 10px;" class="price_vote_points"><span class="price_vote_points">' . ucfirst('price_vote_points') . ' </span><span class="value">' . $votePoints . wp_get_attachment_image(169, 'thumbnail', true, ["class" => "img-responsive", "style" => "width:20px;float:right;"]) . '</span></p></div>';
            }
            $return = $return . '</div>';
            if ($showBuy == true) {
                $return .= '<div class="form-group"><label for="item_set_' . $this->item_set_id . '_character">Select the character to receive this item set</label>
                    <select class="form-control selectCharacter item_set" id="item_set_' . $this->item_set_id . '_character">';
                $i = 0;
                foreach ($this->getCharacters() as $character) {
                    if ($i == 0 AND !isset($this->character)) {
                        $this->character = $character["name"];
                    }
                    if ($this->character == $character["name"]) {
                        $return .= '<option selected value="' . $character["name"] . '">' . $character["name"] . ' lvl ' . $character["level"] . '</option>';
                    } else {
                        $return .= '<option value="' . $character["name"] . '">' . $character["name"] . ' lvl ' . $character["level"] . '</option>';
                    }
                    $i++;
                }
                $characterSelectedIsDeleted = true;
                foreach ($this->getCharacters() as $character) {
                    if ($this->character == $character["name"]) {
                        $characterSelectedIsDeleted = false;
                    }
                }
                if ($characterSelectedIsDeleted == true) {
                    foreach ($this->getCharacters() as $character) {
                        $this->character = $character["name"];
                        break;
                    }
                }
                $return .= '</select>';
                $return .= '<div class="form-group"><label>Quantity</label><select class="form-control quantity item_set" id="item_set_' . $this->item_set_id . '">';
                for ($i = 1; $i <= 100; $i++) {
                    $return .= '<option value="' . $i . '">' . $i . '</option>';
                }
                $return .= '</select></div>';
                $return .= '<div class="radio col-sm-4 col-sm-offset-1 col-xs-6 text-center" style="margin-top: 0px">';
                $return .= wp_get_attachment_image(168, 'thumbnail', true, ["class" => "img-responsive"]);
                if ($this->currency == "buy") {
                    $return .= '<label><input id="' . $this->item_set_id . '" class="item_set buy currency" checked="checked" type="radio" name="optradio_item_set' . $this->item_set_id . '"></label>';
                } else {
                    $return .= '<label><input id="' . $this->item_set_id . '" class="item_set buy currency" type="radio" name="optradio_item_set' . $this->item_set_id . '"></label>';
                }
                $return .= '</div>';
                if ($this->vote == 1) {
                    $return .= '<div class="radio col-sm-4 col-sm-offset-1 col-xs-6 text-center" style="margin-top: 0px">';
                    $return .= wp_get_attachment_image(169, 'thumbnail', true, ["class" => "img-responsive"]);
                    if ($this->currency == "vote") {
                        $return .= '<label><input id="' . $this->item_set_id . '" class="item_set vote currency" checked="checked" type="radio" name="optradio_item_set' . $this->item_set_id . '"></label>';
                    } else {
                        $return .= '<label><input id="' . $this->item_set_id . '" class="item_set vote currency" type="radio" name="optradio_item_set' . $this->item_set_id . '"></label>';
                    }
                    $return .= '</div>';
                }
            }
            $return = $return . '</li></a>';
            if ($directBuy == true) {
                $return = $return . '<div class="col-sm-1 col-xs-12 noPadding displayRadioDirectBuy">';
                if ($selectThisDirectBuy == true) {
                    $return .= '<div class="col-xs-12 noPadding">
			<label class="btn btn-success active">
				<input class="hidden" type="radio" name="item_set_for_item_home_character" value="' . $this->item_set_id . '" autocomplete="off" checked>
				<span class="glyphicon glyphicon-ok"></span>
			</label>
			</div>';
                } else {
                    $return .= '<div class="col-xs-12 noPadding">
			<label class="btn btn-success">
				<input class="hidden" type="radio" name="item_set_for_item_home_character" value="' . $this->item_set_id . '" autocomplete="off">
				<span class="glyphicon glyphicon-ok"></span>
			</label>
			</div>';
                }
                $return .= '</div></div>';
            }
        }
        return $return;
    }

    function getAllItemInfoOfTheSet($allID)
    {
        $result = array();
        $req = 'SELECT * FROM `item` WHERE `item_id` = ' . $allID[0];
        $i = 0;
        foreach ($allID as $idItem) {
            if ($i > 0) {
                $req = $req . ' OR `item_id` = ' . $allID[$i];
            }
            $i++;
        }
        $req = $GLOBALS["dbh"]->query($req);
        if ($req == false) {
            return null;
        } else {
            $i = 0;
            while ($data = $req->fetch(PDO::FETCH_ASSOC)) {
                $i++;
                $result['itemLevel'][$i] = $data["itemLevel"];
                $result['icon'][$i] = $data["icon"];
                $result['requiredLevel'][$i] = $data["requiredLevel"];
                $i++;
            }
            return $result;
        }
    }

    public function displayCart()
    {
        $allInfos = $this->getAllItemInfoOfTheSet($this->items);
        $return = "<div class='col-xs-12 noPadding cartItem' style='margin: 5px 0px'>
        <div class='removeItem'><i onclick=\"removeItemCart(this," . $this->item_set_id . ",'item_set')\" class=\"fa fa-times\" aria-hidden=\"true\"></i></div>
        ";
        foreach ($allInfos["icon"] as $oneImage) {
            $return = $return . ' <img style = "float: left;width: 30px;" src = "https://wow.zamimg.com/images/wow/icons/large/' . $oneImage . '.jpg" alt = "' . $oneImage . '" > ';
        }
        $return .= "<p>" . $this->name . "</p>";
        $return = $return . "<div class='col-xs-12'><hr style='width: 100 %;margin: 0 auto;'/></div>";
        $return = $return . "</div>";
        return $return;
    }
}