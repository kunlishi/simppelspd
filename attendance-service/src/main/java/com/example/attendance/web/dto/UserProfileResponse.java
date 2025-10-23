package com.example.attendance.web.dto;

import com.example.attendance.model.User;

public class UserProfileResponse {
    private Long id;
    private String username;
    private String nim;
    private String fullName;
    private String kelas;
    private String email;
    private String role;

    public static UserProfileResponse from(User user) {
        UserProfileResponse res = new UserProfileResponse();
        res.id = user.getId();
        res.username = user.getUsername();
        res.nim = user.getNim();
        res.fullName = user.getFullName();
        res.kelas = user.getKelas();
        res.email = user.getEmail();
        res.role = user.getRole().name();
        return res;
    }

    public Long getId() { return id; }
    public String getUsername() { return username; }
    public String getNim() { return nim; }
    public String getFullName() { return fullName; }
    public String getKelas() { return kelas; }
    public String getEmail() { return email; }
    public String getRole() { return role; }
}
