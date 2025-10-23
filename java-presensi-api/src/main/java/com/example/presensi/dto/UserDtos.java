package com.example.presensi.dto;

import jakarta.validation.constraints.Email;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.Size;
import lombok.Data;

@Data
public class UserDtos {
    @Data
    public static class ProfileResponse {
        private Long id;
        private String name;
        private String email;
        private String nim;
        private String role;
    }

    @Data
    public static class UpdateProfileRequest {
        @NotBlank
        private String name;
        @Email
        @NotBlank
        private String email;
    }

    @Data
    public static class ChangePasswordRequest {
        @NotBlank
        private String oldPassword;
        @NotBlank
        @Size(min = 6)
        private String newPassword;
    }
}
