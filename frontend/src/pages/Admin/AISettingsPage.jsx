import React, { useState, useEffect } from 'react';
import { 
  Box, 
  TextField, 
  Button, 
  Typography, 
  Paper, 
  CircularProgress, 
  Alert, 
  FormControl,
  InputLabel,
  Select,
  MenuItem,
  Slider,
  Grid
} from '@mui/material';
import axios from 'axios';

const AISettingsPage = () => {
  const [settings, setSettings] = useState({
    api_key: '',
    model: 'gpt-4',
    temperature: 0.7,
    max_tokens: 1000
  });
  const [isLoading, setIsLoading] = useState(true);
  const [isSaving, setIsSaving] = useState(false);
  const [error, setError] = useState(null);
  const [success, setSuccess] = useState(null);

  useEffect(() => {
    // Fetch AI settings
    const fetchSettings = async () => {
      try {
        const response = await axios.get('/api/settings/ai');
        if (response.data.status === 'success') {
          setSettings(response.data.data);
        }
      } catch (err) {
        console.error('Error fetching AI settings:', err);
        setError('حدث خطأ أثناء جلب إعدادات الذكاء الاصطناعي.');
      } finally {
        setIsLoading(false);
      }
    };

    fetchSettings();
  }, []);

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setSettings(prev => ({
      ...prev,
      [name]: value
    }));
  };

  const handleSliderChange = (name) => (event, newValue) => {
    setSettings(prev => ({
      ...prev,
      [name]: newValue
    }));
  };
  const handleSave = async () => {
    setIsSaving(true);
    setError(null);
    setSuccess(null);

    // Validate input
    if (settings.api_key && !settings.api_key.startsWith('sk-') && !settings.api_key.includes('********')) {
      setError('مفتاح API غير صالح. يجب أن يبدأ بـ sk-');
      setIsSaving(false);
      return;
    }

    try {
      // Don't send API key if it's masked (unchanged)
      const dataToSend = { ...settings };
      if (dataToSend.api_key && dataToSend.api_key.includes('********')) {
        // User didn't change the API key, so don't send it
        delete dataToSend.api_key;
      }

      const response = await axios.post('/api/settings/ai', dataToSend);
      if (response.data.status === 'success') {
        setSuccess('تم حفظ إعدادات الذكاء الاصطناعي بنجاح.');
        
        // Refresh settings to get the masked API key
        const refreshResponse = await axios.get('/api/settings/ai');
        if (refreshResponse.data.status === 'success') {
          setSettings(refreshResponse.data.data);
        }
      } else {
        setError(response.data.message || 'حدث خطأ أثناء حفظ الإعدادات.');
      }
    } catch (err) {
      console.error('Error saving AI settings:', err);
      const errorMessage = err.response?.data?.message || 'حدث خطأ أثناء حفظ إعدادات الذكاء الاصطناعي.';
      setError(errorMessage);
    } finally {
      setIsSaving(false);
    }
  };

  if (isLoading) {
    return (
      <Box display="flex" justifyContent="center" alignItems="center" minHeight="300px">
        <CircularProgress />
      </Box>
    );
  }

  return (
    <Paper elevation={3} sx={{ p: 4, m: 2 }}>
      <Typography variant="h4" component="h1" gutterBottom>
        إعدادات الذكاء الاصطناعي
      </Typography>
      
      {error && <Alert severity="error" sx={{ mb: 2 }}>{error}</Alert>}
      {success && <Alert severity="success" sx={{ mb: 2 }}>{success}</Alert>}
      
      <Box component="form" noValidate autoComplete="off" sx={{ mt: 3 }}>
        <Grid container spacing={3}>
          <Grid item xs={12}>
            <TextField
              label="مفتاح API لـ OpenAI"
              name="api_key"
              type="password"
              value={settings.api_key}
              onChange={handleInputChange}
              fullWidth
              helperText="مفتاح API من OpenAI - يبدأ بـ sk-..."
            />
          </Grid>
          
          <Grid item xs={12} md={6}>
            <FormControl fullWidth>
              <InputLabel>نموذج GPT</InputLabel>              <Select
                name="model"
                value={settings.model}
                label="نموذج GPT"
                onChange={handleInputChange}
              >
                <MenuItem value="gpt-3.5-turbo">GPT-3.5 Turbo</MenuItem>
                <MenuItem value="gpt-4">GPT-4</MenuItem>
                <MenuItem value="gpt-4-turbo">GPT-4 Turbo</MenuItem>
                <MenuItem value="gpt-4o">GPT-4o</MenuItem>
              </Select>
            </FormControl>
          </Grid>
          
          <Grid item xs={12} md={6}>
            <TextField
              label="الحد الأقصى للرموز"
              name="max_tokens"
              type="number"
              value={settings.max_tokens}
              onChange={handleInputChange}
              fullWidth
              InputProps={{ inputProps: { min: 100, max: 4000 } }}
              helperText="عدد الرموز (tokens) الأقصى المسموح للنص المُنشأ"
            />
          </Grid>
          
          <Grid item xs={12}>
            <Typography gutterBottom>درجة الإبداع (Temperature): {settings.temperature}</Typography>
            <Slider
              value={settings.temperature}
              onChange={handleSliderChange('temperature')}
              step={0.1}
              marks
              min={0}
              max={1}
              valueLabelDisplay="auto"
              aria-labelledby="temperature-slider"
            />
            <Typography variant="caption" color="textSecondary">
              القيمة الأقل تجعل الإنتاج أكثر تحديدًا والقيمة الأعلى تجعله أكثر إبداعًا وتنوعًا
            </Typography>
          </Grid>
          
          <Grid item xs={12}>
            <Button
              variant="contained"
              color="primary"
              onClick={handleSave}
              disabled={isSaving}
              sx={{ mt: 2 }}
            >
              {isSaving ? <CircularProgress size={24} /> : 'حفظ الإعدادات'}
            </Button>
          </Grid>
        </Grid>
      </Box>
    </Paper>
  );
};

export default AISettingsPage;
