package com.example.presensi.controller;

import com.example.presensi.dto.AttendanceDtos;
import com.example.presensi.model.Role;
import com.example.presensi.model.User;
import com.example.presensi.service.AttendanceService;
import com.example.presensi.service.UserService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.tags.Tag;
import jakarta.validation.Valid;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.security.core.Authentication;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/attendance")
@Tag(name = "Attendance")
public class AttendanceController {
    private final AttendanceService attendanceService;
    private final UserService userService;

    public AttendanceController(AttendanceService attendanceService, UserService userService) {
        this.attendanceService = attendanceService;
        this.userService = userService;
    }

    private User currentUser(Authentication authentication) {
        String email = (String) authentication.getPrincipal();
        return userService.getByEmail(email);
    }

    @PostMapping("/scan")
    @PreAuthorize("hasRole('SPD') or hasRole('ADMIN')")
    @Operation(summary = "Scan QR NIM untuk presensi hari ini")
    public ResponseEntity<AttendanceDtos.AttendanceResponse> scan(Authentication authentication,
                                                                  @Valid @RequestBody AttendanceDtos.ScanRequest req) {
        User officer = currentUser(authentication);
        return ResponseEntity.ok(attendanceService.scan(req.getNim(), officer.getEmail()));
    }

    @GetMapping("/my")
    @Operation(summary = "Mahasiswa: daftar presensi saya")
    public ResponseEntity<?> my(Authentication authentication) {
        User student = currentUser(authentication);
        var list = attendanceService.listForStudent(student).stream().map(a -> {
            AttendanceDtos.AttendanceResponse dto = new AttendanceDtos.AttendanceResponse();
            dto.setId(a.getId());
            dto.setNim(a.getStudent().getNim());
            dto.setName(a.getStudent().getName());
            dto.setDate(a.getDate());
            dto.setScannedAt(a.getScannedAt());
            return dto;
        }).toList();
        return ResponseEntity.ok(list);
    }
}
