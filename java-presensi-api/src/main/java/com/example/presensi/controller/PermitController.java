package com.example.presensi.controller;

import com.example.presensi.dto.PermitDtos;
import com.example.presensi.model.PermitStatus;
import com.example.presensi.model.Role;
import com.example.presensi.model.User;
import com.example.presensi.service.PermitService;
import com.example.presensi.service.UserService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.tags.Tag;
import jakarta.validation.Valid;
import org.springframework.core.io.FileSystemResource;
import org.springframework.http.HttpHeaders;
import org.springframework.http.MediaType;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.security.core.Authentication;
import org.springframework.web.bind.annotation.*;
import org.springframework.web.multipart.MultipartFile;

import java.io.File;
import java.io.IOException;
import java.util.List;

@RestController
@RequestMapping("/api/permit")
@Tag(name = "Permit")
public class PermitController {

    private final PermitService permitService;
    private final UserService userService;

    public PermitController(PermitService permitService, UserService userService) {
        this.permitService = permitService;
        this.userService = userService;
    }

    private User currentUser(Authentication authentication) {
        String email = (String) authentication.getPrincipal();
        return userService.getByEmail(email);
    }

    @PostMapping(value = "/create", consumes = MediaType.MULTIPART_FORM_DATA_VALUE)
    @PreAuthorize("hasRole('STUDENT')")
    @Operation(summary = "Mahasiswa mengajukan izin/sakit dengan bukti foto")
    public ResponseEntity<PermitDtos.PermitResponse> create(Authentication authentication,
                                                            @Valid @RequestPart("data") PermitDtos.CreatePermitRequest req,
                                                            @RequestPart(value = "file", required = false) MultipartFile file) throws IOException {
        User student = currentUser(authentication);
        return ResponseEntity.ok(permitService.create(student, req, file));
    }

    @GetMapping("/my")
    @PreAuthorize("hasRole('STUDENT')")
    @Operation(summary = "Daftar pengajuan milik mahasiswa")
    public ResponseEntity<List<?>> my(Authentication authentication) {
        User student = currentUser(authentication);
        return ResponseEntity.ok(permitService.listForStudent(student));
    }

    @GetMapping("/evidence/{id}")
    @PreAuthorize("hasRole('STUDENT') or hasRole('ADMIN')")
    @Operation(summary = "Ambil file bukti (owner atau admin)")
    public ResponseEntity<FileSystemResource> evidence(Authentication authentication, @PathVariable Long id) throws IOException {
        // Only owner (student) or admin can access
        User me = currentUser(authentication);
        var permit = permitService.getById(id);
        if (!(me.getRole().name().equals("ADMIN") || (permit.getStudent() != null && permit.getStudent().getId().equals(me.getId())))) {
            return ResponseEntity.status(403).build();
        }
        FileSystemResource resource = permitService.evidenceResource(id);
        String contentType = MediaType.APPLICATION_OCTET_STREAM_VALUE;
        try {
            contentType = java.nio.file.Files.probeContentType(resource.getFile().toPath());
        } catch (Exception ignored) { }
        return ResponseEntity.ok()
                .header(HttpHeaders.CONTENT_DISPOSITION, "inline; filename=" + resource.getFilename())
                .contentType(MediaType.parseMediaType(contentType != null ? contentType : MediaType.APPLICATION_OCTET_STREAM_VALUE))
                .body(resource);
    }

    @GetMapping("/pending")
    @PreAuthorize("hasRole('ADMIN')")
    @Operation(summary = "Admin: daftar pengajuan PENDING")
    public ResponseEntity<List<?>> pending() {
        return ResponseEntity.ok(permitService.listByStatus(PermitStatus.PENDING));
    }

    @PostMapping("/{id}/review")
    @PreAuthorize("hasRole('ADMIN')")
    @Operation(summary = "Admin: setujui/tolak pengajuan")
    public ResponseEntity<PermitDtos.PermitResponse> review(Authentication authentication,
                                                            @PathVariable Long id,
                                                            @RequestParam boolean approve) {
        User admin = currentUser(authentication);
        return ResponseEntity.ok(permitService.review(id, approve, admin.getEmail()));
    }
}
