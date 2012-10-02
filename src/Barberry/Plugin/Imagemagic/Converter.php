<?php
namespace Barberry\Plugin\Imagemagic;
use Barberry\Plugin;
use Barberry\ContentType;

class Plugin_Imagemagic_Converter implements Plugin\InterfaceConverter {

    /**
     * @var string
     */
    private $tempPath;

    /**
     * @var ContentType
     */
    private $targetContentType;

    public function __construct(ContentType $targetContentType, $tempPath) {
        $this->tempPath = $tempPath;
        $this->targetContentType = $targetContentType;
    }

    public function convert($bin, Plugin\InterfaceCommand $command = null) {
        $resize = ($command->width()||$command->height())?"-resize ".$command->width()."x".$command->height():"";
        $source = tempnam($this->tempPath, "imagemagick_");
        chmod($source, 0664);
        $destination = $source . '.' . $this->targetContentType->standartExtention();
        file_put_contents($source, $bin);
        exec(
            'convert '.$resize.' '.$source.' '.$destination
        );
        if (is_file($destination)) {
            $bin = file_get_contents($destination);
            unlink($destination);
        }
        unlink($source);

        return $bin;
    }
}
