<?php

namespace splash\altsfinder\utils;

use pocketmine\network\mcpe\protocol\types\DeviceOS;

final class DeviceTranslator {

    public static function toString(int $device): string
    {
        return match ($device){
            DeviceOS::ANDROID => "Android",
            DeviceOS::IOS => "iOS",
            DeviceOS::OSX => "Osx",
            DeviceOS::AMAZON => "Amazon",
            DeviceOS::GEAR_VR => "Gear VR",
            DeviceOS::HOLOLENS => "Hololens",
            DeviceOS::WINDOWS_10 => "Win10",
            DeviceOS::WIN32 => "Win32",
            DeviceOS::DEDICATED => "Dedicated",
            DeviceOS::TVOS => "TvOS",
            DeviceOS::NINTENDO => "Nintendo",
            DeviceOS::PLAYSTATION => "PlayStation",
            DeviceOS::XBOX => "Xbox",
            DeviceOS::WINDOWS_PHONE => "Windows Phone",
            default => "Unknown"
        };
    }

}