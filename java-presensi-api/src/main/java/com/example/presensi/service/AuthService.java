package com.example.presensi.service;

import com.example.presensi.dto.AuthDtos;
import com.example.presensi.model.Role;
import com.example.presensi.model.User;
import com.example.presensi.repository.UserRepository;
import com.example.presensi.security.JwtUtil;
import org.springframework.security.crypto.password.PasswordEncoder;
import org.springframework.stereotype.Service;
import org.springframework.transaction.annotation.Transactional;

import java.util.HashMap;
import java.util.Map;

@Service
public class AuthService {

    private final UserRepository userRepository;
    private final PasswordEncoder passwordEncoder;
    private final JwtUtil jwtUtil;

    public AuthService(UserRepository userRepository, PasswordEncoder passwordEncoder, JwtUtil jwtUtil) {
        this.userRepository = userRepository;
        this.passwordEncoder = passwordEncoder;
        this.jwtUtil = jwtUtil;
    }

    @Transactional
    public AuthDtos.TokenResponse register(AuthDtos.RegisterRequest req, Role role) {
        if (userRepository.existsByEmail(req.getEmail())) {
            throw new IllegalArgumentException("Email sudah terdaftar");
        }
        if (req.getNim() != null && !req.getNim().isBlank() && userRepository.existsByNim(req.getNim())) {
            throw new IllegalArgumentException("NIM sudah terdaftar");
        }
        User user = new User();
        user.setName(req.getName());
        user.setEmail(req.getEmail());
        user.setPasswordHash(passwordEncoder.encode(req.getPassword()));
        user.setRole(role);
        user.setNim(req.getNim());
        userRepository.save(user);
        return buildToken(user);
    }

    public AuthDtos.TokenResponse login(AuthDtos.LoginRequest req) {
        User user = userRepository.findByEmail(req.getEmail()).orElseThrow(() -> new IllegalArgumentException("Email atau password salah"));
        if (!passwordEncoder.matches(req.getPassword(), user.getPasswordHash())) {
            throw new IllegalArgumentException("Email atau password salah");
        }
        return buildToken(user);
    }

    private AuthDtos.TokenResponse buildToken(User user) {
        Map<String, Object> claims = new HashMap<>();
        claims.put("role", user.getRole().name());
        claims.put("uid", user.getId());
        claims.put("name", user.getName());
        String token = jwtUtil.generateToken(user.getEmail(), claims);
        AuthDtos.TokenResponse resp = new AuthDtos.TokenResponse();
        resp.setToken(token);
        resp.setRole(user.getRole().name());
        resp.setUserId(user.getId());
        resp.setName(user.getName());
        resp.setEmail(user.getEmail());
        resp.setNim(user.getNim());
        return resp;
    }
}
