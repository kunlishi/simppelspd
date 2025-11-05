package com.example.attendance.web.dto;

import com.example.attendance.model.User;

public class AuthResponse {
    private String token;
    private Long id;
    private String username;
    private String role;
    private String nim;
    private String fullName;
    private String kelas;
    private String email;

    public static AuthResponse from(User user, String token) {
        AuthResponse res = new AuthResponse();
        res.token = token;
        res.id = user.getId();
        res.username = user.getUsername();
        res.role = user.getRole().name();
        res.nim = user.getNim();
        res.fullName = user.getFullName();
        res.kelas = user.getKelas();
        res.email = user.getEmail();
        return res;
    }

    public String getToken() { return token; }
    public Long getId() { return id; }
    public String getUsername() { return username; }
    public String getRole() { return role; }
    public String getNim() { return nim; }
    public String getFullName() { return fullName; }
    public String getKelas() { return kelas; }
    public String getEmail() { return email; }
}
