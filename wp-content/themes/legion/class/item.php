<?php

//For array
//https://us.battle.net/forums/en/bnet/topic/20743006350#2

class item
{
    public $item_id = null;
    public $description = null;
    public $name = null;
    public $icon = null;
    public $stackable = null;
    public $allowableClasses = null;
    public $bonusStats = null;
    public $itemSpells = null;
    public $itemClass = null;
    public $itemSubClass = null;
    public $itemLevel = null;
    public $sellPrice = null;
    public $requiredLevel = null;
    public $equippable = null;
    public $isAuctionable = null;
    public $containerSlots = null;
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
    }

    public function hydrateBDD($data)
    {
        foreach ($data as $key => $value) {
            if (is_numeric($value)) {
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
                    $req = $req . "'" . json_encode($value) . "'";
                } else {
                    $req = $req . "'" . $value . "'";
                }
            } else {
                if (is_int($value) OR is_bool($value)) {
                    $req = $req . ", " . intval($value);
                } elseif (is_array($value)) {
                    $req = $req . ", '" . json_encode($value) . "'";
                } else {
                    $req = $req . ", '" . $value . "'";
                }
            }
            $i++;
        }
        $req = $req . ")";
        return $req;
    }

    public function display()
    {

    }
}