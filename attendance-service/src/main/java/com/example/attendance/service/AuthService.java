package com.example.attendance.service;

import com.example.attendance.model.Role;
import com.example.attendance.model.User;
import com.example.attendance.repo.UserRepository;
import com.example.attendance.security.JwtService;
import com.example.attendance.web.dto.AuthResponse;
import com.example.attendance.web.dto.LoginRequest;
import com.example.attendance.web.dto.RegisterRequest;
import org.springframework.security.authentication.AuthenticationManager;
import org.springframework.security.authentication.UsernamePasswordAuthenticationToken;
import org.springframework.security.core.Authentication;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.HashMap;
import java.util.Map;

@Service
public class AuthService {

    private final UserRepository userRepository;
    private final PasswordEncoder passwordEncoder;
    private final AuthenticationManager authenticationManager;
    private final JwtService jwtService;

    public AuthService(UserRepository userRepository, PasswordEncoder passwordEncoder, AuthenticationManager authenticationManager, JwtService jwtService) {
        this.userRepository = userRepository;
        this.passwordEncoder = passwordEncoder;
        this.authenticationManager = authenticationManager;
        this.jwtService = jwtService;
    }

    @Transactional
    public AuthResponse registerStudent(RegisterRequest req) {
        if (userRepository.existsByUsername(req.getUsername())) {
            throw new IllegalArgumentException("Username already used");
        }
        if (req.getNim() != null && userRepository.existsByNim(req.getNim())) {
            throw new IllegalArgumentException("NIM already used");
        }
        User user = new User();
        user.setUsername(req.getUsername());
        user.setPassword(passwordEncoder.encode(req.getPassword()));
        user.setNim(req.getNim());
        user.setFullName(req.getFullName());
        user.setKelas(req.getKelas());
        user.setEmail(req.getEmail());
        user.setRole(Role.STUDENT);
        userRepository.save(user);
        Map<String, Object> claims = new HashMap<>();
        claims.put("role", user.getRole().name());
        String token = jwtService.generateToken(user.getUsername(), claims);
        return AuthResponse.from(user, token);
    }

    public AuthResponse login(LoginRequest req) {
        Authentication auth = authenticationManager.authenticate(
                new UsernamePasswordAuthenticationToken(req.getUsername(), req.getPassword())
        );
        User user = userRepository.findByUsername(req.getUsername()).orElseThrow();
        Map<String, Object> claims = new HashMap<>();
        claims.put("role", user.getRole().name());
        String token = jwtService.generateToken(user.getUsername(), claims);
        return AuthResponse.from(user, token);
    }
}
