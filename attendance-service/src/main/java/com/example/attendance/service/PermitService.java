package com.example.attendance.service;

import com.example.attendance.model.Permit;
import com.example.attendance.model.PermitStatus;
import com.example.attendance.model.PermitType;
import com.example.attendance.model.User;
import com.example.attendance.repo.PermitRepository;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;
import org.springframework.web.multipart.MultipartFile;

import java.io.IOException;
import java.time.LocalDate;
import java.util.List;

@Service
public class PermitService {
    private final PermitRepository permitRepository;
    private final FileStorageService fileStorageService;

    public PermitService(PermitRepository permitRepository, FileStorageService fileStorageService) {
        this.permitRepository = permitRepository;
        this.fileStorageService = fileStorageService;
    }

    @Transactional
    public Permit submit(User student, LocalDate forDate, PermitType type, String reason, MultipartFile proof) throws IOException {
        Permit p = new Permit();
        p.setStudent(student);
        p.setForDate(forDate);
        p.setType(type);
        p.setReason(reason);
        if (proof != null && !proof.isEmpty()) {
            String stored = fileStorageService.store(proof);
            p.setProofPath(stored);
        }
        p.setStatus(PermitStatus.PENDING);
        return permitRepository.save(p);
    }

    public List<Permit> myPermits(User student) {
        return permitRepository.findByStudentOrderByCreatedAtDesc(student);
    }

    public List<Permit> pendingPermits() {
        return permitRepository.findByStatusOrderByCreatedAtAsc(PermitStatus.PENDING);
    }

    @Transactional
    public Permit approve(Long id, String note) {
        Permit p = permitRepository.findById(id).orElseThrow();
        p.setStatus(PermitStatus.APPROVED);
        p.setAdminNote(note);
        return p;
    }

    @Transactional
    public Permit reject(Long id, String note) {
        Permit p = permitRepository.findById(id).orElseThrow();
        p.setStatus(PermitStatus.REJECTED);
        p.setAdminNote(note);
        return p;
    }
}
