package com.example.attendance.web;

import com.example.attendance.model.Permit;
import com.example.attendance.model.PermitType;
import com.example.attendance.model.User;
import com.example.attendance.repo.UserRepository;
import com.example.attendance.service.FileStorageService;
import com.example.attendance.service.PermitService;
import org.springframework.core.io.Resource;
import org.springframework.core.io.UrlResource;
import org.springframework.format.annotation.DateTimeFormat;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;

import java.io.IOException;
import java.net.MalformedURLException;
import java.nio.file.Path;
import java.time.LocalDate;
import java.util.List;

@RestController
@RequestMapping("/api/permits")
public class PermitController {

    private final PermitService permitService;
    private final UserRepository userRepository;
    private final FileStorageService fileStorageService;

    public PermitController(PermitService permitService, UserRepository userRepository, FileStorageService fileStorageService) {
        this.permitService = permitService;
        this.userRepository = userRepository;
        this.fileStorageService = fileStorageService;
    }

    @PostMapping(value = "", consumes = MediaType.MULTIPART_FORM_DATA_VALUE)
    public ResponseEntity<Permit> submit(@AuthenticationPrincipal UserDetails principal,
                                         @RequestParam @DateTimeFormat(iso = DateTimeFormat.ISO.DATE) LocalDate forDate,
                                         @RequestParam PermitType type,
                                         @RequestParam(required = false) String reason,
                                         @RequestParam(required = false, name = "proof") MultipartFile proof) throws IOException {
        User student = userRepository.findByUsername(principal.getUsername()).orElseThrow();
        return ResponseEntity.ok(permitService.submit(student, forDate, type, reason, proof));
    }

    @GetMapping("")
    public ResponseEntity<List<Permit>> myPermits(@AuthenticationPrincipal UserDetails principal) {
        User student = userRepository.findByUsername(principal.getUsername()).orElseThrow();
        return ResponseEntity.ok(permitService.myPermits(student));
    }

    @GetMapping("/pending")
    @PreAuthorize("hasRole('ADMIN')")
    public ResponseEntity<List<Permit>> pending() {
        return ResponseEntity.ok(permitService.pendingPermits());
    }

    @PostMapping("/{id}/approve")
    @PreAuthorize("hasRole('ADMIN')")
    public ResponseEntity<Permit> approve(@PathVariable Long id, @RequestParam(required = false) String note) {
        return ResponseEntity.ok(permitService.approve(id, note));
    }

    @PostMapping("/{id}/reject")
    @PreAuthorize("hasRole('ADMIN')")
    public ResponseEntity<Permit> reject(@PathVariable Long id, @RequestParam(required = false) String note) {
        return ResponseEntity.ok(permitService.reject(id, note));
    }

    @GetMapping("/proof/{filename}")
    public ResponseEntity<Resource> getProof(@PathVariable String filename) throws MalformedURLException {
        Path path = fileStorageService.resolve(filename);
        Resource resource = new UrlResource(path.toUri());
        if (!resource.exists()) return ResponseEntity.notFound().build();
        return ResponseEntity.ok()
                .header(HttpHeaders.CONTENT_DISPOSITION, "inline; filename=" + filename)
                .contentType(MediaType.APPLICATION_OCTET_STREAM)
                .body(resource);
    }
}
