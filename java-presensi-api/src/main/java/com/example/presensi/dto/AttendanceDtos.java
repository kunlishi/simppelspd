package com.example.presensi.dto;

import jakarta.validation.constraints.NotBlank;
import lombok.Data;

import java.time.Instant;
import java.time.LocalDate;

@Data
public class AttendanceDtos {
    @Data
    public static class ScanRequest {
        @NotBlank
        private String nim; // from QR
    }

    @Data
    public static class AttendanceResponse {
        private Long id;
        private String nim;
        private String name;
        private LocalDate date;
        private Instant scannedAt;
    }
}
