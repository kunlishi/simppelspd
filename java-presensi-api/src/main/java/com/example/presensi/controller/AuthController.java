package com.example.presensi.controller;

import com.example.presensi.dto.AuthDtos;
import com.example.presensi.model.Role;
import com.example.presensi.service.AuthService;
import io.swagger.v3.oas.annotations.Operation;
import io.swagger.v3.oas.annotations.tags.Tag;
import jakarta.validation.Valid;
import org.springframework.http.ResponseEntity;
import org.springframework.web.bind.annotation.*;

@RestController
@RequestMapping("/api/auth")
@Tag(name = "Auth")
public class AuthController {

    private final AuthService authService;

    public AuthController(AuthService authService) {
        this.authService = authService;
    }

    @PostMapping("/register-student")
    @Operation(summary = "Registrasi mahasiswa")
    public ResponseEntity<AuthDtos.TokenResponse> registerStudent(@Valid @RequestBody AuthDtos.RegisterRequest req) {
        return ResponseEntity.ok(authService.register(req, Role.STUDENT));
    }

    @PostMapping("/register-spd")
    @Operation(summary = "Registrasi petugas SPD")
    public ResponseEntity<AuthDtos.TokenResponse> registerSpd(@Valid @RequestBody AuthDtos.RegisterRequest req) {
        // nim optional for SPD
        return ResponseEntity.ok(authService.register(req, Role.SPD));
    }

    @PostMapping("/register-admin")
    @Operation(summary = "Registrasi admin (sementara terbuka untuk demo)")
    public ResponseEntity<AuthDtos.TokenResponse> registerAdmin(@Valid @RequestBody AuthDtos.RegisterRequest req) {
        return ResponseEntity.ok(authService.register(req, Role.ADMIN));
    }

    @PostMapping("/login")
    @Operation(summary = "Login")
    public ResponseEntity<AuthDtos.TokenResponse> login(@Valid @RequestBody AuthDtos.LoginRequest req) {
        return ResponseEntity.ok(authService.login(req));
    }
}
