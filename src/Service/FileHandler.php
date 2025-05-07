<?php

declare(strict_types=1);


namespace App\Service;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

class FileHandler extends AbstractController
{

    public function __construct(private readonly SluggerInterface $slugger) { }

    public function handle(mixed $file, string $directory): ?string
    {
        try {
            $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $safeFilename = $this->slugger->slug($originalFilename);
            $newFileName = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

            $file->move($this->getParameter($directory), $newFileName);

            return $newFileName;
        } catch (\Exception $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return null;
    }
}