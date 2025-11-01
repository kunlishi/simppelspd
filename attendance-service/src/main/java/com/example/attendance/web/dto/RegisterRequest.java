package com.example.attendance.web.dto;

import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Size;

public class RegisterRequest {
    @NotBlank
    private String username; // usually NIM

    @NotBlank
    @Size(min = 6)
    private String password;

    private String nim;
    private String fullName;
    private String kelas;
    @Email
    private String email;

    public String getUsername() { return username; }
    public void setUsername(String username) { this.username = username; }
    public String getPassword() { return password; }
    public void setPassword(String password) { this.password = password; }
    public String getNim() { return nim; }
    public void setNim(String nim) { this.nim = nim; }
    public String getFullName() { return fullName; }
    public void setFullName(String fullName) { this.fullName = fullName; }
    public String getKelas() { return kelas; }
    public void setKelas(String kelas) { this.kelas = kelas; }
    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }
}
