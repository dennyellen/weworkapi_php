<?php

include_once(__DIR__."/../../utils/Utils.class.php");

class Department
{
    public $name = null; // string
    public $parentid = null; // uint
    public $order = null; // uint
    public $id = null; // uint

    public static function Department2Array($department)
    {
        $args = array();

        Utils::setIfNotNull($department->name, "name", $args);
        Utils::setIfNotNull($department->parentid, "parentid", $args);
        Utils::setIfNotNull($department->order, "order", $args);
        Utils::setIfNotNull($department->id, "id", $args);

        return $args;
    }

    public static function Array2Department($arr)
    {
        $department = new Department();

        $department->name = Utils::arrayGet($arr, "name");
        $department->parentid = Utils::arrayGet($arr, "parentid");
        $department->order = Utils::arrayGet($arr, "order");
        $department->id = Utils::arrayGet($arr, "id");

        return $department;
    }

    public static function Array2DepartmentList($arr)
    {
        $list = $arr["department"];

        $departmentList = array();
        if (is_array($list)) {
            foreach ($list as $item) {
                $department = self::Array2Department($item);
                $departmentList[] = $department;
            }
        }
        return $departmentList;
    }

    public static function CheckDepartmentCreateArgs($department)
    {
        Utils::checkNotEmptyStr($department->name, "department name");
        Utils::checkIsUInt($department->parentid, "parentid");
    }

    public static function CheckDepartmentUpdateArgs($department)
    {
        Utils::checkIsUInt($department->id, "department id");
    }
} // class
