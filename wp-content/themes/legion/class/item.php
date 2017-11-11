<?php

//For array
//https://us.battle.net/forums/en/bnet/topic/20743006350#2

class item
{
    public $item_id = null;
    public $name = null;
    public $itemLevel = null;
    public $itemClass = null;
    public $itemSubClass = null;
    public $armor = null;
    public $maxDurability = null;
    public $bonusStats = null;
    public $allowableClasses = null;
    public $requiredLevel = null;
    public $itemSpells = null;
    public $description = null;
    public $icon = null;
    public $stackable = null;
    public $equippable = null;
    public $isAuctionable = null;
    public $containerSlots = null;
    public $itemSet = null;
    public $sellPrice = null;
    public $price = null;

    public function hydrateAPI($data)
    {
        if (isset($data->id)) {
            $this->item_id = $data->id;
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
        if (isset($data->stackable)) {
            $this->stackable = $data->stackable;
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
                "71" => "Strenght, Agility orIntelect",
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
            $this->requiredLevel = $data->requiredLevel;
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
    }

    public function hydrateBDD($data)
    {
        foreach ($data as $key => $value) {
            if (is_int($value)) {
                $this->$key = intval($value);
            } else {
                $newValue = json_decode($value);
                if ($newValue != NULL) {
                    $this->$key = $newValue;
                } else {
                    $this->$key = $value;
                }
            }
        }
    }

    public function generateInsertRequest()
    {
        $req = "INSERT INTO `item`(";
        $i = 0;
        foreach ($this as $key => $value) {
            if ($i == 0) {
                $req = $req . "`" . $key . "`";
            } else {
                $req = $req . ", `" . $key . "`";
            }
            $i++;
        }
        $req = $req . ") VALUES (";
        $i = 0;
        foreach ($this as $key => $value) {
            if ($i == 0) {
                if (is_int($value) OR is_bool($value)) {
                    $req = $req . intval($value);
                } elseif (is_array($value)) {
                    $req = $req . "'" . addslashes(json_encode($value)) . "'";
                } else {
                    $req = $req . "'" . addslashes($value) . "'";
                }
            } else {
                if (is_int($value) OR is_bool($value)) {
                    $req = $req . ", " . intval($value);
                } elseif (is_array($value)) {
                    $req = $req . ", '" . addslashes(json_encode($value)) . "'";
                } else {
                    $req = $req . ", '" . addslashes($value) . "'";
                }
            }
            $i++;
        }
        $req = $req . ")";
        return $req;
    }

    public function display($itemClass, $small = false)
    {
        $return = '';
        $tab = ["name", "requiredLevel", "stackable", "allowableClasses", "itemLevel"];
        if ($small == true) {
            $dataShow["value"] = $this->item_id;
            $dataShow["id"] = "previewItem";
            $json = json_encode($dataShow);
            $return = $return . "<a data-show='" . $json . "' onclick='showMoreShop(this)'><li class='list-group-item col-sm-4 col-xs-12'><span style='display:none'>";
            if ($this->allowableClasses != null AND sizeof($this->allowableClasses) > 0) {
                foreach ($this->allowableClasses as $allowableClass) {
                    $return = $return . ' ' . $allowableClass . ' ' . $this->requiredLevel;
                }
            }
            $return = $return . '</span>';
        }
        $return = $return . '
        <div class="display_item">
            <img style="float: left" src="https://wow.zamimg.com/images/wow/icons/large/' . $this->icon . '.jpg" alt="' . $this->name . '" />
        ';
        foreach ($this as $key => $value) {
            if (($small == true AND in_array($key, $tab)) OR $small == false) {
                if ($key == "itemClass") {
                    $return = $return . '<p class="' . ucfirst($key) . '"><span class="' . $key . '">' . $key . ' </span><span class="value">' . $itemClass->name . '</span><i class="fa fa-arrow-right" style="margin-left: 5px;margin-right: 5px;" aria-hidden="true"></i></p>';
                } elseif ($key == "itemSubClass") {
                    $return = $return . '<p class="' . ucfirst($key) . '"><span class="' . $key . '">' . $key . ' </span><span class="value">' . $itemClass->getSubClassName($this) . '</span></p>';
                } elseif ($key == "itemSpells") {
                    if ($value != "[]") {
                        $return = $return . '<p class="' . $key . '">Item Spells :</p>';
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
                                if ($value == 1 OR true) {
                                    $value = "Yes";
                                } else {
                                    $value = "No";
                                }
                            } else if ($key == "maxDurability") {
                                $return = $return . '<p class="' . $key . '"><span class="' . $key . '">Durability </span><span class="value">' . $value . '</span></p>';
                            } else if ($key == "sellPrice") {
                                $return = $this->displaySellPrice($return, $key, $value);
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
                            } else {
                                $return = $return . '<p class="' . $key . '"><span class="' . $key . '">' . ucfirst($key) . ' </span><span class="value">' . $value . '</span></p>';
                            }
                        }
                    }
                }
            }
        }
        $return = $return . "</div></li>";
        return $return;
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

    function displaySellPrice($return, $key, $value)
    {
        $homePageId = get_option('page_on_front');
        $image = get_field("money_gold", $homePageId);
        $imgGold = wp_get_attachment_image($image["id"], 'full', "", ["class" => "img-responsive"]);
        $image = get_field("money_silver", $homePageId);
        $imgSilver = wp_get_attachment_image($image["id"], 'full', "", ["class" => "img-responsive"]);
        $image = get_field("money_copper", $homePageId);
        $imgCopper = wp_get_attachment_image($image["id"], 'full', "", ["class" => "img-responsive"]);
        $globalArray = array_map('intval', str_split($value));
        $globalArray = array_reverse($globalArray);
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
}