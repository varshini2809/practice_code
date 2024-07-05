import { useState } from "react";
import "./TODOList.css";

const DEFAULT_TRACKS = [
  {
    title: "Track 1",
    artist: "Artist 1",
    album: "Album 1",
    duration: "180",
  },
  {
    title: "Track 2",
    artist: "Artist 2",
    album: "Album 2",
    duration: "200",
  },
  {
    title: "Track 3",
    artist: "Artist 3",
    album: "Album 3",
    duration: "240",
  },
];

function Form({ addTrack, currentTrack, updateTrack, isEditing }) {
  const [title, setTitle] = useState(currentTrack?.title || "");
  const [artist, setArtist] = useState(currentTrack?.artist || "");
  const [album, setAlbum] = useState(currentTrack?.album || "");
  const [duration, setDuration] = useState(currentTrack?.duration || "");
  const [src, setSrc] = useState(currentTrack?.src || "");

  const handleSubmit = (event) => {
    event.preventDefault();
    const newTrack = {
      title,
      artist,
      album,
      duration,
      src,
    };
    if (isEditing) {
      updateTrack(newTrack);
    } else {
      addTrack(newTrack);
    }
    setTitle("");
    setArtist("");
    setAlbum("");
    setDuration("");
    setSrc("");
  };

  return (
    <form className="form" onSubmit={handleSubmit}>
      <h2>{isEditing ? "Edit Track" : "Add New Track"}</h2>
      <div className="form-group">
        <label>Title:</label>
        <input
          type="text"
          value={title}
          onChange={(e) => setTitle(e.target.value)}
          required
        />
      </div>
      <div className="form-group">
        <label>Artist:</label>
        <input
          type="text"
          value={artist}
          onChange={(e) => setArtist(e.target.value)}
          required
        />
      </div>
      <div className="form-group">
        <label>Album:</label>
        <input
          type="text"
          value={album}
          onChange={(e) => setAlbum(e.target.value)}
          required
        />
      </div>
      <div className="form-group">
        <label>Duration:</label>
        <input
          type="text"
          value={duration}
          onChange={(e) => setDuration(e.target.value)}
          required
        />
      </div>
      <button type="submit">{isEditing ? "Update Track" : "Add Track"}</button>
    </form>
  );
}

function TrackCard({ track, onDelete, onEdit }) {
  return (
    <div className="track-card">
      <div>
        <strong>Title:</strong> {track.title}
      </div>
      <div>
        <strong>Artist:</strong> {track.artist}
      </div>
      <div>
        <strong>Album:</strong> {track.album}
      </div>
      <div>
        <strong>Duration:</strong> {track.duration} seconds
      </div>
      <div className="track-card-buttons">
        <button onClick={() => onEdit(track)}>Edit</button>
        <button onClick={() => onDelete(track)}>Delete</button>
      </div>
    </div>
  );
}

function TODOList() {
  const [tracks, setTracks] = useState(DEFAULT_TRACKS);
  const [searchQuery, setSearchQuery] = useState("");
  const [editingTrack, setEditingTrack] = useState(null);

  const addTrack = (newTrack) => {
    setTracks([...tracks, newTrack]);
  };

  const deleteTrack = (trackToDelete) => {
    const updatedTracks = tracks.filter((track) => track !== trackToDelete);
    setTracks(updatedTracks);
  };

  const updateTrack = (updatedTrack) => {
    const updatedTracks = tracks.map((track) =>
      track === editingTrack ? updatedTrack : track
    );
    setTracks(updatedTracks);
    setEditingTrack(null);
  };

  const handleEdit = (track) => {
    setEditingTrack(track);
  };

  const filteredTracks = tracks.filter((track) =>
    track.artist.toLowerCase().includes(searchQuery.toLowerCase())
  );

  return (
    <div className="music-player">
      <h1>Simple Music Player</h1>
      <div className="search-box">
        <input
          type="text"
          placeholder="Search by artist"
          value={searchQuery}
          onChange={(e) => setSearchQuery(e.target.value)}
        />
      </div>
      <Form
        addTrack={addTrack}
        currentTrack={editingTrack}
        updateTrack={updateTrack}
        isEditing={!!editingTrack}
      />
      <div className="track-list">
        {filteredTracks.map((track, index) => (
          <TrackCard
            key={index}
            track={track}
            onDelete={deleteTrack}
            onEdit={handleEdit}
          />
        ))}
      </div>
    </div>
  );
}

export default TODOList;
