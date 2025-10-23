package com.example.presensi.config;

import io.swagger.v3.oas.models.ExternalDocumentation;
import io.swagger.v3.oas.models.OpenAPI;
import io.swagger.v3.oas.models.info.Info;
import io.swagger.v3.oas.models.info.License;
import org.springframework.context.annotation.Bean;
import org.springframework.context.annotation.Configuration;

@Configuration
public class OpenApiConfig {

    @Bean
    public OpenAPI springOpenAPI() {
        return new OpenAPI()
                .info(new Info().title("Presensi & Izin API")
                        .description("API untuk presensi apel dan pengajuan izin mahasiswa")
                        .version("v1.0")
                        .license(new License().name("MIT")))
                .externalDocs(new ExternalDocumentation()
                        .description("Dokumentasi Tambahan")
                        .url("https://example.com/docs"));
    }
}
