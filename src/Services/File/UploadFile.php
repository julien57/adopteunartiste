<?php

namespace App\Services\File;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class UploadFile
{
    const AVATAR_UPLOAD_DIR = 'img/upload/avatars/';
    const COVERUPLOAD_DIR = 'img/upload/covers/';
    const PHOTO_POST_UPLOAD_DIR = 'img/upload/posts/';
    const PHOTO_MESSAGING_UPLOAD_DIR = 'img/upload/messaging/';

    /**
     * @param UploadedFile $file
     * @return string
     * @throws \Exception
     */
    public static function uploadAvatar(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move(
                self::AVATAR_UPLOAD_DIR,
                $newFilename
            );
        } catch (FileException $e) {
            throw new \Exception('Error upload file');
        }

        return $newFilename;
    }

    /**
     * @param UploadedFile $file
     * @return string
     * @throws \Exception
     */
    public static function uploadCover(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move(
                self::COVERUPLOAD_DIR,
                $newFilename
            );
        } catch (FileException $e) {
            throw new \Exception('Error upload file');
        }

        return $newFilename;
    }

    /**
     * @param UploadedFile $file
     * @return string
     * @throws \Exception
     */
    public static function uploadPhotoPost(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move(
                self::PHOTO_POST_UPLOAD_DIR,
                $newFilename
            );
        } catch (FileException $e) {
            throw new \Exception('Error upload file');
        }

        return $newFilename;
    }

    /**
     * @param UploadedFile $file
     * @return string
     * @throws \Exception
     */
    public static function uploadPhotoMessaging(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        // this is needed to safely include the file name as part of the URL
        $safeFilename = transliterator_transliterate('Any-Latin; Latin-ASCII; [^A-Za-z0-9_] remove; Lower()', $originalFilename);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        try {
            $file->move(
                self::PHOTO_MESSAGING_UPLOAD_DIR,
                $newFilename
            );
        } catch (FileException $e) {
            throw new \Exception('Error upload file');
        }

        return $newFilename;
    }
}
