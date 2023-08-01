<?php

namespace mvc\View;

use DateInterval;

class CalendarFileView
{
    private string $content;

    public function __construct()
    {
        $this->content = <<<ICS
BEGIN:VCALENDAR
VERSION:2.0
PRODID:-//UDE//ZOW 2023//DE

ICS;
    }

    function addEvent(\DateTime $start, int $duration, string $title, string $desc, string $location): void
    { //teilweise von ChatGPT
        // Escape-Sonderzeichen im Zusammenfassungstext
        $title = str_replace(array("\\", ";", ","), array("\\\\", "\\;", "\\,"), $title);

        // Start- und Endzeit im iCalendar-Datumsformat (UTC)
        $start_st = $start->format('Ymd\THis\Z');
        $end = $start_st;
        try {
            $hours = floor($duration / 60);
            $minutes = $duration % 60;
            // Füge Stunden und Minuten hinzu
            $start->add(new DateInterval('PT' . $hours . 'H' . $minutes . 'M'));
            $end = $start->format('Ymd\THis\Z');
        } catch (\Exception) {

        }

        // Ereignis-Details als iCalendar-Text
        $eventText = "BEGIN:VEVENT\r\n";
        $eventText .= "DTSTART:{$start_st}\r\n";
        $eventText .= "DTEND:{$end}\r\n";
        $eventText .= "SUMMARY:{$title}\r\n";
        $eventText .= "DESCRIPTION:$desc\r\n";
        $eventText .= "LOCATION:$location\r\n";
        $eventText .= "END:VEVENT\r\n";

        $this->content .= $eventText;
    }

    function generateIcsContent(): string
    {
        return $this->content . "END:VCALENDAR\r\n";
    }
}