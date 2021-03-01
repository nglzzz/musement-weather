import React, {Component} from 'react';
import axios from 'axios';

class Home extends Component {

  constructor() {
    super();
    this.state = {
      cities: [],
      loadingCities: true,
      loadingForecast: true,
      citySelected: '',
      daysSelected: 2,
      forecast: {},
    };
  }

  componentDidMount() {
    this.getCities();
    this.getForecast();
  }

  getCities = () => {
    axios.get('/api/v3/cities').then(cities => {
      this.setState({
        cities: cities.data,
        loadingCities: false
      })
    });
  }

  getForecast = () => {
    let params = {
      days: this.state.daysSelected,
    }

    if (this.state.citySelected) {
      params.city = this.state.citySelected;
    }

    axios.get('/api/v3/forecast/', {
      params: params,
    }).then(forecast => {
      this.setState({
        forecast: forecast.data,
        loadingForecast: false
      })
    });
  }

  handleChangeCity = (event) => {
    this.setState({
      citySelected: event.target.value
    });
  }

  handleChangeDays = (event) => {
    this.setState({
      daysSelected: event.target.value
    });
  }

  handleSubmitFiltration = (event) => {
    this.setState({
      loadingForecast: true
    });

    this.getForecast();
    event.preventDefault();
  }

  renderFiltration = () => {
    const loadingCities = this.state.loadingCities;
    return (
      <form onSubmit={this.handleSubmitFiltration}>
        <h2>Filtration</h2>
        <p>
          City:
          {loadingCities ? (
            <select>
              <option>Loading...</option>
            </select>
          ) : (
            <select value={this.state.citySelected} onChange={this.handleChangeCity}>
              <option value="">All</option>
              { this.state.cities.map(city =>
                <option key={city.id} value={city.id}>{city.name}</option>
              ) }
            </select>
          )}
        </p>
        <p>
          Days:
          <select value={this.state.daysSelected} onChange={this.handleChangeDays} defaultValue={2}>
            <option value="1">1</option>
            <option value="2">2</option>
            <option value="3">3</option>
          </select>
        </p>
        <button>Submit</button>
      </form>
    );
  }

  renderForecast = () => {
    return (
      <>
        <h2>Forecast</h2>

        {this.state.loadingForecast ? (
          <div>Loading forecast...</div>
        ) : (
          <>
            <table border="1">
            {this.state.forecast.map(item =>
              <tr>
                <td>{item.location}</td>
                {Object.entries(item.forecast).map(([k, v]) => (
                  <td key={k}>
                    {v}
                  </td>
                ))}
              </tr>
            )}
            </table>
          </>
        )}
      </>
    )
  }

  render() {
    return (
      <>
        {this.renderFiltration()}
        {this.renderForecast()}
      </>
    )
  }
}

export default Home;
