package com.example.presensi.model;

import jakarta.persistence.*;
import lombok.Getter;
import lombok.Setter;

import java.time.Instant;

@Entity
@Getter
@Setter
public class PermitRequest {
    @Id
    @GeneratedValue(strategy = GenerationType.IDENTITY)
    private Long id;

    @ManyToOne(optional = false)
    private User student;

    @Enumerated(EnumType.STRING)
    private PermitType type; // SICK or PERMIT

    private String reason;

    private String evidencePath; // stored file path

    @Enumerated(EnumType.STRING)
    private PermitStatus status = PermitStatus.PENDING;

    private Instant createdAt = Instant.now();

    private Instant reviewedAt;

    private String reviewedBy; // admin email
}
