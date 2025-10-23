package com.example.presensi.dto;

import com.example.presensi.model.PermitStatus;
import com.example.presensi.model.PermitType;
import jakarta.validation.constraints.NotBlank;
import jakarta.validation.constraints.NotNull;
import lombok.Data;

import java.time.Instant;

@Data
public class PermitDtos {
    @Data
    public static class CreatePermitRequest {
        @NotNull
        private PermitType type;
        @NotBlank
        private String reason;
        // file is multipart
    }

    @Data
    public static class PermitResponse {
        private Long id;
        private PermitType type;
        private String reason;
        private String evidenceUrl;
        private PermitStatus status;
        private Instant createdAt;
        private Instant reviewedAt;
    }
}
