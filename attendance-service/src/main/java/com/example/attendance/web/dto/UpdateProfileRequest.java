package com.example.attendance.web.dto;

import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.Size;

public class UpdateProfileRequest {
    private String fullName;
    private String kelas;
    @Email
    private String email;

    public String getFullName() { return fullName; }
    public void setFullName(String fullName) { this.fullName = fullName; }
    public String getKelas() { return kelas; }
    public void setKelas(String kelas) { this.kelas = kelas; }
    public String getEmail() { return email; }
    public void setEmail(String email) { this.email = email; }
}
