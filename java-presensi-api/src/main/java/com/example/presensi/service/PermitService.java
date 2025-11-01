package com.example.presensi.service;

import com.example.presensi.dto.PermitDtos;
import com.example.presensi.model.PermitRequest;
import com.example.presensi.model.PermitStatus;
import com.example.presensi.model.User;
import com.example.presensi.repository.PermitRepository;
import org.springframework.core.io.FileSystemResource;
import org.springframework.beans.factory.annotation.Value;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.web.multipart.MultipartFile;

import java.io.IOException;
import java.nio.file.Files;
import java.nio.file.Path;
import java.nio.file.Paths;
import java.time.Instant;
import java.util.List;

@Service
public class PermitService {

    private final PermitRepository permitRepository;

    private final Path uploadDir;

    public PermitService(PermitRepository permitRepository, @Value("${app.uploadDir}") String uploadDir) throws IOException {
        this.permitRepository = permitRepository;
        this.uploadDir = Paths.get(uploadDir);
        Files.createDirectories(this.uploadDir);
    }

    @Transactional
    public PermitDtos.PermitResponse create(User student, PermitDtos.CreatePermitRequest req, MultipartFile file) throws IOException {
        String filename = null;
        if (file != null && !file.isEmpty()) {
            String ext = "";
            String original = file.getOriginalFilename();
            if (original != null && original.contains(".")) {
                ext = original.substring(original.lastIndexOf('.'));
            }
            filename = "evidence-" + System.currentTimeMillis() + ext;
            Path dest = uploadDir.resolve(filename);
            Files.copy(file.getInputStream(), dest);
        }
        PermitRequest pr = new PermitRequest();
        pr.setStudent(student);
        pr.setType(req.getType());
        pr.setReason(req.getReason());
        pr.setEvidencePath(filename);
        pr.setStatus(PermitStatus.PENDING);
        pr.setCreatedAt(Instant.now());
        PermitRequest saved = permitRepository.save(pr);
        return toDto(saved);
    }

    public List<PermitRequest> listForStudent(User student) {
        return permitRepository.findByStudent(student);
    }

    public List<PermitRequest> listByStatus(PermitStatus status) {
        return permitRepository.findByStatus(status);
    }

    @Transactional
    public PermitDtos.PermitResponse review(Long id, boolean approve, String reviewer) {
        PermitRequest pr = permitRepository.findById(id).orElseThrow(() -> new IllegalArgumentException("Pengajuan tidak ditemukan"));
        pr.setStatus(approve ? PermitStatus.APPROVED : PermitStatus.REJECTED);
        pr.setReviewedAt(Instant.now());
        pr.setReviewedBy(reviewer);
        return toDto(permitRepository.save(pr));
    }

    private PermitDtos.PermitResponse toDto(PermitRequest pr) {
        PermitDtos.PermitResponse dto = new PermitDtos.PermitResponse();
        dto.setId(pr.getId());
        dto.setType(pr.getType());
        dto.setReason(pr.getReason());
        dto.setEvidenceUrl(pr.getEvidencePath() != null ? "/api/permit/evidence/" + pr.getId() : null);
        dto.setStatus(pr.getStatus());
        dto.setCreatedAt(pr.getCreatedAt());
        dto.setReviewedAt(pr.getReviewedAt());
        return dto;
    }

    public FileSystemResource evidenceResource(Long id) {
        PermitRequest pr = permitRepository.findById(id).orElseThrow(() -> new IllegalArgumentException("Pengajuan tidak ditemukan"));
        if (pr.getEvidencePath() == null) {
            throw new IllegalArgumentException("Tidak ada bukti untuk pengajuan ini");
        }
        Path path = uploadDir.resolve(pr.getEvidencePath());
        return new FileSystemResource(path.toFile());
    }

    public PermitRequest getById(Long id) {
        return permitRepository.findById(id).orElseThrow(() -> new IllegalArgumentException("Pengajuan tidak ditemukan"));
    }
}
