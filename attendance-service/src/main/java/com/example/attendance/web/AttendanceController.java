package com.example.attendance.web;

import com.example.attendance.model.Attendance;
import com.example.attendance.model.User;
import com.example.attendance.repo.UserRepository;
import com.example.attendance.service.AttendanceService;
import org.springframework.http.ResponseEntity;
import org.springframework.security.access.prepost.PreAuthorize;
import org.springframework.security.core.annotation.AuthenticationPrincipal;
import org.springframework.security.core.userdetails.UserDetails;
import org.springframework.web.bind.annotation.*;

import java.util.List;

@RestController
@RequestMapping("/api/attendance")
public class AttendanceController {

    private final AttendanceService attendanceService;
    private final UserRepository userRepository;

    public AttendanceController(AttendanceService attendanceService, UserRepository userRepository) {
        this.attendanceService = attendanceService;
        this.userRepository = userRepository;
    }

    // Petugas scans QR -> sends NIM
    @PostMapping("/scan/{nim}")
    @PreAuthorize("hasAnyRole('PETUGAS','ADMIN')")
    public ResponseEntity<Attendance> scan(@PathVariable String nim) {
        return ResponseEntity.ok(attendanceService.scanByNim(nim));
    }

    // Student views own attendance history
    @GetMapping("/me")
    public ResponseEntity<List<Attendance>> myAttendance(@AuthenticationPrincipal UserDetails principal) {
        User user = userRepository.findByUsername(principal.getUsername()).orElseThrow();
        return ResponseEntity.ok(attendanceService.history(user));
    }
}
