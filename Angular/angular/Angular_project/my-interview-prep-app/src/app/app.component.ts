import { Component, signal } from '@angular/core';
import { RouterOutlet } from '@angular/router';
import { Header } from "./header/header";
import { Sidebar } from "./sidebar/sidebar";

@Component({
  selector: 'app-root',
  imports: [RouterOutlet, Header, Sidebar],
  templateUrl: './app.component.html',
  styleUrl: './app.component.css'
})
export class AppComponent {
  protected readonly title = signal('my-interview-prep-app');
}
