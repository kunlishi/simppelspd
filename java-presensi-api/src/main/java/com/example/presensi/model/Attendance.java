package com.example.presensi.model;

import jakarta.persistence.*;
import lombok.Getter;
import lombok.Setter;

import java.time.Instant;
import java.time.LocalDate;

@Entity
@Getter
@Setter
public class Attendance {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne(optional = false)
    private User student;

    private LocalDate date; // apel date

    private Instant scannedAt; // exact timestamp

    private String scannedBy; // officer identifier (SPD email or username)
}
