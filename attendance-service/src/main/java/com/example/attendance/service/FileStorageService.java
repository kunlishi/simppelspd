package com.example.attendance.service;

import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;
import org.springframework.util.StringUtils;
import org.springframework.web.multipart.MultipartFile;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.nio.file.StandardCopyOption;
import java.util.UUID;

@Service
public class FileStorageService {
    private final Path rootDir;

    public FileStorageService(@Value("${app.file-storage.upload-dir}") String uploadDir) throws IOException {
        this.rootDir = Paths.get(uploadDir).toAbsolutePath().normalize();
        Files.createDirectories(this.rootDir);
    }

    public String store(MultipartFile file) throws IOException {
        String original = StringUtils.cleanPath(file.getOriginalFilename());
        String ext = "";
        int idx = original.lastIndexOf('.')
                ;
        if (idx >= 0) ext = original.substring(idx);
        String filename = UUID.randomUUID() + ext;
        Path target = rootDir.resolve(filename);
        Files.copy(file.getInputStream(), target, StandardCopyOption.REPLACE_EXISTING);
        return filename;
    }

    public Path resolve(String relativePath) {
        return rootDir.resolve(relativePath).normalize();
    }
}
