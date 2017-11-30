<?php

include_once("parent_item.php");

//For array
//https://us.battle.net/forums/en/bnet/topic/20743006350#2

class item extends parent_item
{
    public $item_id = null;
    public $name = null;
    public $itemLevel = null;
    public $itemClass = null;
    public $itemSubClass = null;
    public $armor = null;
    public $damage = null;
    public $maxDurability = null;
    public $bonusStats = null;
    public $allowableClasses = null;
    public $requiredLevel = null;
    public $itemSpells = null;
    public $description = null;
    public $gemInfo = null;
    public $icon = null;
    public $stackable = null;
    public $equippable = null;
    public $isAuctionable = null;
    public $containerSlots = null;
    public $itemSet = null;
    public $sellPrice = null;
    public $price = 0;
    public $vote = 0;
    public $side = null;
    public $nbSells = 0;

    public function hydrateAPI($data)
    {
        if (isset($data->id)) {
            $this->item_id = $data->id;
        }
        if (isset($data->allowableRaces)) {
            if (in_array(1, $data->allowableRaces)) {
                $this->side = 1;
            } else {
                $this->side = 2;
            }
        }
        if (isset($data->gemInfo)) {
            $this->gemInfo = $data->gemInfo->bonus->name;
        }
        if (isset($data->description)) {
            $this->description = $data->description;
        }
        if (isset($data->name)) {
            $this->name = $data->name;
        }
        if (isset($data->icon)) {
            $this->icon = $data->icon;
        }
        if (isset($data->allowableClasses)) {
            $allClasses = array(
                '1' => 'Warrior',
                '2' => 'Paladin',
                '3' => 'Hunter',
                '4' => 'Rogue',
                '5' => 'Priest',
                '6' => 'Death Knight',
                '7' => 'Shaman',
                '8' => 'Mage',
                '9' => 'Warlock',
                '10' => 'Monk',
                '11' => 'Druid',
                '12' => 'Demon Hunter'
            );
            if (sizeof($data->allowableClasses) > 0) {
                $this->allowableClasses = array();
            }
            foreach ($data->allowableClasses as $idClass) {
                array_push($this->allowableClasses, $allClasses[$idClass]);
            }
        }
        if (isset($data->bonusStats)) {
            $allStats = array(
                "-1" => "None",
                "0" => "Mana",
                "1" => "Health",
                "3" => "Agility",
                "4" => "Strenght",
                "5" => "Intellect",
                "6" => "Spirit",
                "7" => "Stamina",
                //-------------------------------
                "12" => "Defense Skill",
                "13" => "Dodge",
                "14" => "Parry",
                "15" => "Block",
                "16" => "Melee Hit",
                "17" => "Ranged Hit",
                "18" => "Spell Hit",
                "19" => "Melee Crit",
                "20" => "Ranged Crit",
                "21" => "Spell Crit",
                "22" => "Melee Hit Taken",
                "23" => "Ranged Hit Taken",
                "24" => "Spell Hit Taken",
                "25" => "Melee Crit Taken",
                "26" => "Ranged Crit Taken",
                "27" => "Spell Crit Taken",
                "28" => "Melee Haste",
                "29" => "Ranged Haste",
                "30" => "Spell Haste",
                "31" => "Hit",
                "32" => "Crit",
                "33" => "Hit Taken",
                "34" => "Crit Taken",
                "35" => "Resilience",
                "36" => "Haste",
                "37" => "Expertise",
                "38" => "Attack Power",
                "39" => "Ranged Attack Power",
                "40" => "Versatility",
                "41" => "Spell Healing Done", // deprecated
                "42" => "Spell Damage Done", // deprecated
                "43" => "Mana Regeneration",
                "44" => "Armor Penetration",
                "45" => "Spell Power",
                "46" => "Health Regen",
                "47" => "Spell Penetration",
                "48" => "Block Value",
                "49" => "Mastery",
                "50" => "Bonus Armor",
                "51" => "Fire Resistance",
                "52" => "Frost Resistance",
                "53" => "Holy Resistance",
                "54" => "Shadow Resistance",
                "55" => "Nature Resistance",
                "56" => "Arcane Resistance",
                "57" => "PVP Power",
                "58" => "Amplify",
                "66" => "Cleave",
                // new in wod
                "60" => "Readiness",
                "61" => "Speed",
                "62" => "Leech",
                "63" => "Avoidence",
                "64" => "Indestructible",
                "65" => "WOD_5",
                '59' => "Multistrike",
                '73' => "Agility or Intellect",
                "71" => "Strenght or Agility or Intelect",
                "72" => "Strenght or Agility",
                "74" => "Strenght or Intelect",
                // end of new wod
            );
            $this->bonusStats = $data->bonusStats;
            foreach ($this->bonusStats as $bonusStat) {
                $bonusStat->stat = $allStats[$bonusStat->stat];
            }
        }
        if (isset($data->itemSpells)) {
            $this->itemSpells = $data->itemSpells;
        }
        if (isset($data->itemClass)) {
            $this->itemClass = $data->itemClass;
        }
        if (isset($data->itemSubClass)) {
            $this->itemSubClass = $data->itemSubClass;
        }
        if (isset($data->itemLevel)) {
            $this->itemLevel = $data->itemLevel;
        }
        if (isset($data->sellPrice)) {
            $this->sellPrice = $data->sellPrice;
            $this->price = intval($this->sellPrice / 1000);
        }
        if (isset($data->requiredLevel)) {
            if ($data->requiredLevel == 0) {
                $this->requiredLevel = 1;
            } else {
                $this->requiredLevel = $data->requiredLevel;
            }
        }
        if (isset($data->equippable)) {
            $this->equippable = $data->equippable;
        }
        if (isset($data->isAuctionable)) {
            $this->isAuctionable = $data->isAuctionable;
        }
        if (isset($data->containerSlots)) {
            $this->containerSlots = $data->containerSlots;
        }
        if (isset($data->maxDurability)) {
            $this->maxDurability = $data->maxDurability;
        }
        if (isset($data->armor)) {
            $this->armor = $data->armor;
        }
        if (isset($data->itemSet)) {
            $this->itemSet = $data->itemSet->id;
        }
        if (isset($data->stackable)) {
            $this->getStackableNumber($data->stackable);
        }
        if (isset($data->weaponInfo)) {
            $this->damage = $data->weaponInfo->dps;
        }
    }

    public function display($itemClass, $small = false, $display_option = false, $display_admin = false)
    {
        $return = '';
        $tab = ["name", "requiredLevel", "stackable", "allowableClasses", "itemLevel", "side", "price", "promotion"];
        if ($this->itemClass == 0) {//Consumable
            array_push($tab, "itemSpells");
        }
        if ($this->itemClass == 1) {//Container
            array_push($tab, "containerSlots", "description");
        }
        if ($this->itemClass == 2) {//Weapon
            array_push($tab, "bonusStats", "damage");
        }
        if ($this->itemClass == 3) {//Gem
            array_push($tab, "gemInfo");
        }
        if ($this->itemClass == 4) {//Armor
            array_push($tab, "bonusStats");
        }
        if ($this->itemClass == 5) {//Reagent
            array_push($tab, "description");
        }
        if ($this->itemClass == 7) {//Tradeskill
            array_push($tab, "description");
        }
        if ($this->itemClass == 9) {//Recipe
            array_push($tab, "itemSpells");
        }
        if ($this->itemClass == 12) {//Quest
            array_push($tab, "description");
        }
        if ($this->itemClass == 13) {//Key
            array_push($tab, "itemSpells");
        }
        if ($this->itemClass == 15) {//Miscellaneous
            array_push($tab, "description");
        }
        if ($this->itemClass == 16) {//Glyph
            array_push($tab, "itemSpells");
        }
        if ($this->itemClass == 17) {//Battle Pets
            array_push($tab, "itemSpells");
        }
        if ($this->itemClass == 18) {//WoW Token

        }
        if ($small == true) {
            $dataShow["value"] = $this->item_id;
            $dataShow["id"] = "previewItem";
            $json = json_encode($dataShow);
            $return = $return . "<a class='pinterest' data-show='" . $json . "' onclick='showMoreShop(this)'><li class='list-group-item col-sm-4 col-xs-12'><span style='display:none'>";
            if ($this->allowableClasses != null AND sizeof($this->allowableClasses) > 0) {
                foreach ($this->allowableClasses as $allowableClass) {
                    $return = $return . ' ' . $allowableClass . ' ' . $this->requiredLevel;
                }
            }
            $return = $return . '</span>';
        }
        if ($small == true) {
            $return = $return . '<div class="display_item display_item_small">';
        } else {
            $return = $return . '<div class="display_item">';
        }
        $return = $return . '<img style="float: left" src="https://wow.zamimg.com/images/wow/icons/large/' . $this->icon . '.jpg" alt="' . $this->name . '" />
        ';
        foreach ($this as $key => $value) {
            if (($small == true AND in_array($key, $tab)) OR $small == false) {
                if ($key == "itemClass") {
                    $return = $return . '<p class="' . ucfirst($key) . '"><span class="' . $key . '">' . $key . ' </span><span class="value">' . $itemClass->name . '</span><i class="fa fa-arrow-right" style="margin-left: 5px;margin-right: 5px;" aria-hidden="true"></i></p>';
                } elseif ($key == "itemSubClass") {
                    $return = $return . '<p class="' . ucfirst($key) . '"><span class="' . $key . '">' . $key . ' </span><span class="value">' . $itemClass->getSubClassName($this) . '</span></p>';
                } elseif ($key == "itemSpells") {
                    if ($value != "[]") {
                        $return = $return . '<p class="hidden ' . $key . '">Item Spells :</p>';
                        foreach ($value as $newValue) {
                            if (isset($newValue->spell->description)) {
                                $return = $return . '<p class="' . $key . '">' . $newValue->spell->description . '</p>';
                            }
                        }
                    } else {
                        $return = $return . '<p class="' . $key . ' no' . $key . '">Item Spells : None</p>';
                    }
                } elseif (is_array($value)) {
                    $return = $return . '<p class="' . $key . '">';
                    foreach ($value as $newkey => $tabValue) {
                        if (is_object($tabValue)) {
                            $tabValue = $this->objectToArray($tabValue);
                        }
                        if (is_array($tabValue)) {
                            $return = $this->displayArray($return, $tabValue);
                        } else {
                            $return = $return . '<span class="' . str_replace(' ', '_', $tabValue) . '">' . $tabValue . ' </span>';
                        }
                    }
                    $return = $return . '</p>';
                } else {
                    if (is_bool($value)) {
                        if ($value == true) {
                            $value = 'true';
                        } else {
                            $value = 'false';
                        }
                    }
                    if ($value != '') {
                        if (($key == "maxDurability" OR $key == "armor" OR $key == "containerSlots" OR $key == "equippable") AND $value == 0) {

                        } else {
                            if ($key == "isAuctionable" OR $key == "equippable") {
                                $value = intval($value);
                                if ($value == 1) {
                                    $value = "Yes";
                                } else {
                                    $value = "No";
                                }
                                if ($key == "isAuctionable") {
                                    $return = $return . '<p class="' . $key . '"><span class="' . $key . '">Is Auctionable : </span><span class="value">' . $value . '</span></p>';
                                } else {
                                    $return = $return . '<p class="' . $key . '"><span class="' . $key . '">' . ucfirst($key) . ' : </span><span class="value">' . $value . '</span></p>';
                                }
                            } else if ($key == "maxDurability") {
                                $return = $return . '<p class="' . $key . '"><span class="' . $key . '">Durability </span><span class="value">' . $value . '</span></p>';
                            } else if ($key == "sellPrice") {
                                $return = $this->displaySellPrice($return, $value);
                            } else if ($key == "requiredLevel") {
                                $return = $return . '<p class="' . $key . '"><span class="' . $key . '">Required Level </span><span class="value">' . $value . '</span></p>';
                            } else if ($key == "description") {
                                $return = $return . '<p class="' . $key . '"><span class="' . $key . '">' . ucfirst($key) . ' </span><span class="value">"' . $value . '"</span></p>';
                            } elseif ($key == "name") {
                                if ($small == true) {
                                    $return = $return . '<p class="' . $key . '"><span class="' . $key . '">' . ucfirst($key) . ' </span><span class="value">"' . $value . '"</span></p>';
                                } else {
                                    $return = $return . '<p class="' . $key . '"><span class="' . $key . '">' . ucfirst($key) . ' </span><span class="value">' . $value . '</span></p><p style="width: 100%;height: 20px;"></p>';
                                }
                            } elseif ($key == "itemSet" AND $value == 0) {
                                $return = $return . '<p class="' . $key . ' no' . $key . '"><span class="' . $key . '">' . ucfirst($key) . ' </span><span class="value">' . $value . '</span></p>';
                            } elseif ($key == "itemLevel") {
                                $return = $return . '<p class="' . $key . '"><span class="' . $key . '">Item Level </span><span class="value">' . $value . '</span></p>';
                            } elseif ($key == 'stackable' AND $value > 999) {
                                $return = $return . '<p class="' . $key . ' extraplusplus"><span class="' . $key . '">' . ucfirst($key) . ' </span><span class="value">' . $value . '</span></p>';
                            } elseif ($key == 'stackable' AND $value > 99) {
                                $return = $return . '<p class="' . $key . ' extraplus"><span class="' . $key . '">' . ucfirst($key) . ' </span><span class="value">' . $value . '</span></p>';
                            } elseif ($key == 'stackable' AND $value > 9) {
                                $return = $return . '<p class="' . $key . ' extra"><span class="' . $key . '">' . ucfirst($key) . ' </span><span class="value">' . $value . '</span></p>';
                            } elseif ($key == 'side') {
                                if ($value == 0) {
                                } elseif ($value == 1) {
                                    $return = $return . '<p class="' . $key . '"><span class="' . $key . '">' . ucfirst($key) . ' </span><span class="value">' . wp_get_attachment_image(166, "full", true, ["class" => "img-responsive"]) . '</span></p>';
                                } else {
                                    $return = $return . '<p class="' . $key . '"><span class="' . $key . '">' . ucfirst($key) . ' </span><span class="value">' . wp_get_attachment_image(167, "full", true, ["class" => "img-responsive"]) . '</span></p>';
                                }
                            } elseif ($key == "bonusStats") {
                                if ($value != '[]') {
                                    $return = $return . '<p class="' . $key . '"><span class="' . $key . '">' . ucfirst($key) . ' </span><span class="value">' . $value . '</span></p>';
                                }
                            } elseif ($key == "price") {
                                $return = $return . '<p class="' . $key . '"><span class="' . $key . '">' . ucfirst($key) . ' </span><span class="value">' . $value . '</span></p>';
                                $return = $return . '<div class="display_price"><p class="' . $key . '_buy_points"><span class="' . $key . '_buy_points">' . ucfirst($key . '_buy_points') . ' </span><span class="value">' . formatNumber($this->getBuyPoint()) . wp_get_attachment_image(168, 'thumbnail', true, ["class" => "img-responsive", "style" => "width:20px;float:right;"]) . '</span></p>';
                                if ($this->vote == 1) {
                                    $return = $return . '<p style="margin-right: 10px;" class="' . $key . '_vote_points"><span class="' . $key . '_vote_points">' . ucfirst($key . '_vote_points') . ' </span><span class="value">' . formatNumber($this->getVotePoint()) . wp_get_attachment_image(169, 'thumbnail', true, ["class" => "img-responsive", "style" => "width:20px;float:right;"]) . '</span></p></div>';
                                }
                            } elseif ($key == "containerSlots") {
                                $return = $return . '<p class="' . $key . '"><span class="' . $key . '">Container Slots </span><span class="value">' . $value . '</span></p>';
                            } elseif ($key == "promotion") {
                                if ($this->promotion > 0 AND $this->promotion <= 100) {
                                    $image = get_field("logo_promo", $GLOBALS["shop_page_id"]);
                                    $return = $return . wp_get_attachment_image($image["id"], 'medium', "", ["class" => "img-responsive promo"]);
                                    $return = $return . "<span class='promo'>-" . $this->promotion . "%</span>";
                                }
                            } elseif ($key == "nbSells") {

                            } elseif ($key == "item_id") {
                                $return = $return . '<p class="' . $key . '"><span class="' . $key . '">' . ucfirst($key) . ' </span><span class="value"><a target="_blank" href="http://www.wowhead.com/item=' . $this->item_id . '">' . $value . '</a></span></p>';
                            } else {
                                $return = $return . '<p class="' . $key . '"><span class="' . $key . '">' . ucfirst($key) . ' </span><span class="value">' . $value . '</span></p>';
                            }
                        }
                    }
                }
            }
        }
        $return = $return . "</div>";
        if ($display_option == true) {
            $return = $return . "<div style='width: 1px;height: 10px'></div>";
            $return = $return . "<div class='option'><div id='result_req_user_item'></div>
    <button style='float:left;' onclick=\"addToCart(this," . $this->item_id . ",'item')\" type=\"button\" class=\"btn btn-success\">" . wp_get_attachment_image(221, 'thumbnail', true, array('class' => 'img-responsive')) . "</button>";
            if (($this->promotion > 0 AND $this->promotion <= 100) AND $this->time_promotion > time()) {
                $return = $return . "<div style='display: inline-block;float: left;margin-left:15px; position: relative'>";
                $return = $return . wp_get_attachment_image(209, 'thumbnail', true, array('class' => 'img-responsive'));
                $return = $return . "<span class='promo'>-" . $this->promotion . "%</span>";
                $return = $return . "</div>";
            }
            $return = $return . "</div>";
        }
        if ($display_admin == true AND isWowAdmin()) {
            $return = $return . "<div class='col-xs-12' style='padding: 0'>";
            $return = $return . '<hr><div id="ajaxLoaderShopAdmin"></div><div id="result_req_admin_item"></div>
             <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#update_promotion_admin">Promotion</button>
             <button type="button" class="btn btn-info" data-toggle="collapse" data-target="#update_item_admin">Item</button>
                <div id="update_promotion_admin" class="collapse">
                <button onclick="removePromotion(' . $this->item_id . ')" type="button" class="btn btn-danger">Remove promotion</button>
                <p>Update promotion</p>
                <form method="post" id="update_promotion_item_admin">
                    <input name="item_id" class="hidden" value="' . $this->item_id . '" />
                    <input name="pourcent" placeholder="poucentage ex:20" type=\'number\' min="0" max="100" />
                    <input name="date" type=\'date\' placeholder=\'dd/mm/yyyy\' />
                    <button type="submit" class="btn btn-info">Update</button>
                </form>
                </div>
                <div id="update_item_admin" class="collapse">
                <button onclick="removeItem(' . $this->item_id . ')" type="button" class="btn btn-danger">Remove Item</button>
                <form id="update_item_admin">
                    <input name="item_id" class="hidden" value="' . $this->item_id . '" />
                    <input type="number" min="0" name="price" value="' . $this->price . '">
                    <button type="submit" class="btn btn-info">Update</button>
                </form>
                </div> 
            </div>';
        }
        $return = $return . "</li></a>";
        return $return;
    }

    public function displayCart()
    {
        $return = "<div class='col-xs-12 noPadding cartItem' style='margin: 5px 0px'>
        <div class='removeItem'><i onclick=\"removeItemCart(this," . $this->item_id . ",'item')\" class=\"fa fa-times\" aria-hidden=\"true\"></i></div>
        <img style='float: left;width: 30px' class='img-responsive' src=\"https://wow.zamimg.com/images/wow/icons/large/" . $this->icon . ".jpg\" alt=\"" . $this->name . "\" />
        <p>" . $this->name . "</p>";
        $return = $return . "<div class='col-xs-12'><hr style='width: 100%;margin: 0 auto;'/></div>";
        $return = $return . "</div>";
        return $return;
    }

    public function getVotePoint()
    {
        if ($this->promotion > 100 OR $this->promotion < 0) {
            $this->promotion = 0;
        }
        $realVotePrice = ($this->price * VOTE_POINTS);
        $PricePromotion = $realVotePrice - ($realVotePrice * $this->promotion / 100);
        if ($this->promotion > 0 AND $this->promotion <= 100) {
            return '<del>' . intval($realVotePrice) . '</del>' . intval($PricePromotion);
        } else {
            return intval($realVotePrice);
        }
    }

    public function getBuyPoint()
    {
        if ($this->promotion > 100 OR $this->promotion < 0) {
            $this->promotion = 0;
        }
        $realBuyPrice = ($this->price * BUY_POINTS);
        $PricePromotion = $realBuyPrice - ($realBuyPrice * $this->promotion / 100);
        if ($this->promotion > 0 AND $this->promotion <= 100) {
            return '<del>' . intval($realBuyPrice) . '</del>' . intval($PricePromotion);
        } else {
            return intval($realBuyPrice);
        }
    }

    private function objectToArray($d)
    {
        if (is_object($d))
            $d = get_object_vars($d);
        return is_array($d) ? array_map(__METHOD__, $d) : $d;
    }

    private function displayArray($return, $tabValue)
    {
        $i = 0;
        foreach ($tabValue as $newValue) {
            $class = str_replace(' ', '_', $newValue);
            if (is_array($newValue)) {
                $this->displayArray($return, $newValue);
            } else {
                if ($i % 2 == 0) {
                    $return = $return . '<span class="' . $class . '"><span class="plus">+</span><span class="key">' . $newValue . '</span> ';
                } else {
                    $return = $return . '<span class="value">' . $newValue . '</span></span><br/>';
                }
                $i++;
            }
        }
        return $return;
    }

    public function displaySellPrice($return, $value)
    {
        $shopPageId = $GLOBALS["shop_page_id"];
        $image = get_field("money_gold", $shopPageId);
        $imgGold = wp_get_attachment_image($image["id"], 'full', "", ["class" => "img-responsive"]);
        $image = get_field("money_silver", $shopPageId);
        $imgSilver = wp_get_attachment_image($image["id"], 'full', "", ["class" => "img-responsive"]);
        $image = get_field("money_copper", $shopPageId);
        $imgCopper = wp_get_attachment_image($image["id"], 'full', "", ["class" => "img-responsive"]);
        $globalArray = array_map('intval', str_split($value));
        $globalArray = array_reverse($globalArray);
        if (!isset($globalArray[1])) {
            $globalArray[1] = 0;
        }
        if (!isset($globalArray[2])) {
            $globalArray[2] = 0;
        }
        if (!isset($globalArray[3])) {
            $globalArray[3] = 0;
        }
        if (!isset($globalArray[4])) {
            $globalArray[4] = 0;
        }
        $cooperArray = [$globalArray[0], $globalArray[1]];
        $silverArray = [$globalArray[2], $globalArray[3]];
        $goldArray = array();
        foreach ($globalArray as $key => $value) {
            if ($key > 3) {
                array_push($goldArray, $value);
            }
        }
        $cooperArray = array_reverse($cooperArray);
        $silverArray = array_reverse($silverArray);
        $goldArray = array_reverse($goldArray);
        $value = implode($goldArray) . $imgGold . implode($silverArray) . $imgSilver . implode($cooperArray) . $imgCopper;
        $return = $return . '<p class="sellPrice"><span class="value">' . $value . '</span></p>';
        return $return;
    }

    public function getStackableNumber($maxStack)
    {
        $maxStack = intval($maxStack);
        $this->itemClass = intval($this->itemClass);
        $this->stackable = $maxStack;
        if ($this->itemClass == 0) {//Consumable
            $this->stackable = $maxStack;
        }
        if ($this->itemClass == 1) {//Container
            $this->stackable = 1;
        }
        if ($this->itemClass == 2) {//Weapon
            $this->stackable = 1;
        }
        if ($this->itemClass == 3) {//Gem
            $this->stackable = 1;
        }
        if ($this->itemClass == 4) {//Armor
            $this->stackable = 1;
        }
        if ($this->itemClass == 5) {//Reagent
            $this->stackable = $maxStack;
        }
        if ($this->itemClass == 7) {//Tradeskill
            $this->stackable = $maxStack;
        }
        if ($this->itemClass == 9) {//Recipe
            $this->stackable = $maxStack;
        }
        if ($this->itemClass == 12) {//Quest
            $this->stackable = $maxStack;
        }
        if ($this->itemClass == 13) {//Key
            $this->stackable = $maxStack;
        }
        if ($this->itemClass == 15) {//Miscellaneous
            $this->stackable = $maxStack;
        }
        if ($this->itemClass == 16) {//Glyph
            $this->stackable = 1;
        }
        if ($this->itemClass == 17) {//Battle Pets
            $this->stackable = 1;
        }
        if ($this->itemClass == 18) {//WoW Token
            $this->stackable = 1;
        }
    }

    public function generatePrice()
    {
        $maxLevel = 110;
        $this->itemClass = intval($this->itemClass);
        if ($this->itemClass == 0) {//Consumable
            $this->price = 20;
        }
        if ($this->itemClass == 1) {//Container
            if ($this->itemSubClass == 0) {
                $this->price = $this->containerSlots * 3;
            } else {
                $this->price = $this->containerSlots * 2;
            }
        }
        if ($this->itemClass == 3) {//Gem
            $max_price = 50;
            if ($this->itemSubClass != 9 AND $this->itemSubClass != 10 AND $this->itemSubClass != 11) {
                $maxItemLevel = 110;
                $k = ($maxItemLevel * $maxItemLevel) / $max_price;
                if (isset($this->itemLevel) AND $this->itemLevel >= 1) {
                    $this->price = intval(($this->itemLevel * $this->itemLevel) / $k);
                } else {
                    $this->price = $max_price;
                }
            } elseif ($this->itemSubClass != 9) {//Other
                $this->price = $max_price;
            } elseif ($this->itemSubClass != 10) {//Multiple Stats
                $this->price = $max_price;
            } elseif ($this->itemSubClass != 11) {//Artifact Relic
                $this->price = 80;
            }
        }
        if ($this->itemClass == 4 OR $this->itemClass == 2) {//Armor && Weapon
            $max_price = 200;
            $maxItemLevel = 890;
            $k = ($maxLevel * $maxLevel) / $max_price;
            if (isset($this->requiredLevel) AND $this->requiredLevel >= 1) {
                $this->price = intval(($this->requiredLevel * $this->requiredLevel) / $k);
            }
            $k = ($maxItemLevel * $maxItemLevel) / $max_price;
            if (isset($this->itemLevel) AND $this->itemLevel >= 1) {
                $this->price = ($this->price + intval(($this->itemLevel * $this->itemLevel) / $k)) / 2;
            }
            if ($this->price == 0) {
                $this->price = $max_price;
            }
        }
        if ($this->itemClass == 5) {//Reagent
            $this->price = 50;
        }
        if ($this->itemClass == 7) {//Tradeskill
            $maxItemLevel = 110;
            $max_price = 100;
            $k = ($maxItemLevel * $maxItemLevel) / $max_price;
            if ($this->itemLevel > $maxItemLevel) {
                $this->price = $max_price;
            } elseif (isset($this->itemLevel) AND $this->itemLevel >= 1) {
                $this->price = intval(($this->itemLevel * $this->itemLevel) / $k);
            } else {
                $this->price = $max_price;
            }
        }
        if ($this->itemClass == 9) {//Recipe
            $maxItemLevel = 110;
            $max_price = 100;
            $k = ($maxItemLevel * $maxItemLevel) / $max_price;
            if ($this->itemLevel > $maxItemLevel) {
                $this->price = $max_price;
            } elseif (isset($this->itemLevel) AND $this->itemLevel >= 1) {
                $this->price = intval(($this->itemLevel * $this->itemLevel) / $k);
            } else {
                $this->price = $max_price;
            }
        }
        if ($this->itemClass == 12) {//Quest
            $this->price = 50;
        }
        if ($this->itemClass == 13) {//Key
            $this->price = 10;
        }
        if ($this->itemClass == 15) {//Miscellaneous
            if ($this->itemSubClass == 0) {//Junk
                $this->price = 10;
            }
            if ($this->itemSubClass == 1) {//Reagent
                $this->price = 20;
            }
            if ($this->itemSubClass == 2) {//Companion Pets
                $this->price = 40;
            }
            if ($this->itemSubClass == 3) {//Holiday
                $this->price = 30;
            }
            if ($this->itemSubClass == 4) {//Other
                $this->price = 30;
            }
            if ($this->itemSubClass == 5) {//Mount
                $this->price = 800;
            }
        }
        if ($this->itemClass == 16) {//Glyph
            $this->price = 20;
        }
        if ($this->itemClass == 17) {//Battle Pets
            $this->price = 50;
        }
        if ($this->itemClass == 18) {//WoW Token
            $this->price = 100000000;
        }

        if ($this->price <= 0) {
            $this->price = 1000;
        }
    }

    public function generateUpdateRequest()
    {
        unset($this->id);
        $req = "UPDATE `" . get_class($this) . "` SET ";
        $i = 0;
        foreach ($this as $key => $value) {
            if ($i > 0) {
                $req .= ",`" . $key . "` = ";
                if (is_int($value) OR is_bool($value)) {
                    $req = $req . intval($value);
                } elseif (is_array($value)) {
                    $req = $req . "'" . addslashes(json_encode($value)) . "'";
                } else {
                    $req = $req . "'" . addslashes($value) . "'";
                }
            } else {
                $req .= "`" . $key . "` = ";
                if (is_int($value) OR is_bool($value)) {
                    $req = $req . intval($value);
                } elseif (is_array($value)) {
                    $req = $req . "'" . addslashes(json_encode($value)) . "'";
                } else {
                    $req = $req . "'" . addslashes($value) . "'";
                }
            }
            $i++;
        }
        $req .= " WHERE item_id = " . $this->item_id;
        return $req;
    }
}