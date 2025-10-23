package com.example.presensi.dto;

import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Size;
import lombok.Data;

@Data
public class AuthDtos {
    @Data
    public static class RegisterRequest {
        @NotBlank
        private String name;
        @Email
        @NotBlank
        private String email;
        @NotBlank
        @Size(min = 6)
        private String password;
        private String nim; // required for student
    }

    @Data
    public static class LoginRequest {
        @Email
        @NotBlank
        private String email;
        @NotBlank
        private String password;
    }

    @Data
    public static class TokenResponse {
        private String token;
        private String role;
        private Long userId;
        private String name;
        private String email;
        private String nim;
    }
}
